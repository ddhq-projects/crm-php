<?php

declare(strict_types=1);

namespace Crm\Repositories;

use Crm\Core\Database;
use PDO;

final class CaseRepository
{
    public const TYPES = [
        'lead' => 'Lead',
        'issue' => 'Issue',
        'task' => 'Task',
    ];

    public const STATUSES = [
        'new' => 'New',
        'in_progress' => 'In progress',
        'waiting' => 'Waiting',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public function __construct(private readonly Database $db)
    {
    }

    public function all(string $status = '', string $kind = ''): array
    {
        $sql = 'SELECT cases.*, clients.name AS client_name, clients.company AS client_company,
                users.name AS assignee_name, GROUP_CONCAT(contacts.name, \', \') AS contact_names
                FROM cases
                LEFT JOIN clients ON clients.id = cases.client_id
                LEFT JOIN users ON users.id = cases.assignee_id
                LEFT JOIN case_contacts ON case_contacts.case_id = cases.id
                LEFT JOIN contacts ON contacts.id = case_contacts.contact_id';
        $parameters = [];
        $conditions = [];

        if (isset(self::STATUSES[$status])) {
            $conditions[] = 'cases.status = ?';
            $parameters[] = $status;
        }
        if (isset(self::TYPES[$kind])) {
            $conditions[] = 'cases.kind = ?';
            $parameters[] = $kind;
        }
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' GROUP BY cases.id ORDER BY cases.updated_at DESC, cases.id DESC';
        $statement = $this->db->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT cases.*, clients.name AS client_name, clients.company AS client_company, users.name AS assignee_name
             FROM cases
             LEFT JOIN clients ON clients.id = cases.client_id
             LEFT JOIN users ON users.id = cases.assignee_id
             WHERE cases.id = ?'
        );
        $statement->execute([$id]);
        $case = $statement->fetch();
        if ($case === false) {
            return null;
        }

        $case['contact_ids'] = $this->contactIds($id);

        return $case;
    }

    public function byClient(int $clientId): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT cases.*, users.name AS assignee_name FROM cases
             LEFT JOIN users ON users.id = cases.assignee_id
             WHERE client_id = ? ORDER BY updated_at DESC, id DESC'
        );
        $statement->execute([$clientId]);

        return $statement->fetchAll();
    }

    public function byContact(int $contactId): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT cases.* FROM cases
             JOIN case_contacts ON case_contacts.case_id = cases.id
             WHERE case_contacts.contact_id = ? ORDER BY cases.updated_at DESC, cases.id DESC'
        );
        $statement->execute([$contactId]);

        return $statement->fetchAll();
    }

    public function recent(int $limit = 6): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT cases.*, clients.name AS client_name, users.name AS assignee_name
             FROM cases
             LEFT JOIN clients ON clients.id = cases.client_id
             LEFT JOIN users ON users.id = cases.assignee_id
             ORDER BY cases.updated_at DESC, cases.id DESC LIMIT ?'
        );
        $statement->bindValue(1, $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function statusSummary(): array
    {
        $result = [];
        foreach (self::STATUSES as $key => $label) {
            $result[$key] = ['label' => $label, 'count' => 0];
        }

        $rows = $this->db->pdo->query('SELECT status, COUNT(*) AS count FROM cases GROUP BY status')->fetchAll();
        foreach ($rows as $row) {
            if (isset($result[$row['status']])) {
                $result[$row['status']]['count'] = (int) $row['count'];
            }
        }

        return $result;
    }

    public function stats(): array
    {
        return $this->db->pdo->query(
            "SELECT COUNT(*) AS total,
             COALESCE(SUM(CASE WHEN status NOT IN ('resolved', 'closed') THEN 1 ELSE 0 END), 0) AS open,
             COALESCE(SUM(CASE WHEN status NOT IN ('resolved', 'closed') AND assignee_id IS NULL THEN 1 ELSE 0 END), 0) AS unassigned
             FROM cases"
        )->fetch();
    }

    public function create(array $data): array
    {
        $now = date('Y-m-d H:i:s');
        $this->db->pdo->beginTransaction();
        try {
            $statement = $this->db->pdo->prepare(
                'INSERT INTO cases (client_id, assignee_id, title, kind, status, priority, due_date, amount, description, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $statement->execute([
                $data['client_id'], $data['assignee_id'], $data['title'], $data['kind'], $data['status'],
                $data['priority'], $data['due_date'], $data['amount'], $data['description'], $now, $now,
            ]);
            $id = (int) $this->db->pdo->lastInsertId();
            $this->syncContacts($id, $data['contact_ids']);
            $this->db->pdo->commit();
        } catch (\Throwable $exception) {
            $this->db->pdo->rollBack();
            throw $exception;
        }

        return $this->find($id);
    }

    public function update(int $id, array $data): array
    {
        $this->db->pdo->beginTransaction();
        try {
            $statement = $this->db->pdo->prepare(
                'UPDATE cases SET client_id = ?, assignee_id = ?, title = ?, kind = ?, status = ?, priority = ?,
                 due_date = ?, amount = ?, description = ?, updated_at = ? WHERE id = ?'
            );
            $statement->execute([
                $data['client_id'], $data['assignee_id'], $data['title'], $data['kind'], $data['status'],
                $data['priority'], $data['due_date'], $data['amount'], $data['description'], date('Y-m-d H:i:s'), $id,
            ]);
            $this->syncContacts($id, $data['contact_ids']);
            $this->db->pdo->commit();
        } catch (\Throwable $exception) {
            $this->db->pdo->rollBack();
            throw $exception;
        }

        return $this->find($id);
    }

    public function updateStatus(int $id, string $status): array
    {
        $statement = $this->db->pdo->prepare('UPDATE cases SET status = ?, updated_at = ? WHERE id = ?');
        $statement->execute([$status, date('Y-m-d H:i:s'), $id]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->pdo->prepare('DELETE FROM cases WHERE id = ?');
        $statement->execute([$id]);
    }

    private function contactIds(int $caseId): array
    {
        $statement = $this->db->pdo->prepare('SELECT contact_id FROM case_contacts WHERE case_id = ?');
        $statement->execute([$caseId]);

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    private function syncContacts(int $caseId, array $contactIds): void
    {
        $this->db->pdo->prepare('DELETE FROM case_contacts WHERE case_id = ?')->execute([$caseId]);
        $statement = $this->db->pdo->prepare('INSERT INTO case_contacts (case_id, contact_id) VALUES (?, ?)');
        foreach ($contactIds as $contactId) {
            $statement->execute([$caseId, $contactId]);
        }
    }
}
