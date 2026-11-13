<?php

declare(strict_types=1);

namespace Crm\Repositories;

use Crm\Core\Database;

final class ContactRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function all(string $search = ''): array
    {
        $sql = 'SELECT contacts.*, clients.name AS client_name, clients.company AS client_company,
                COUNT(cases.id) AS cases_count
                FROM contacts
                LEFT JOIN clients ON clients.id = contacts.client_id
                LEFT JOIN case_contacts ON case_contacts.contact_id = contacts.id
                LEFT JOIN cases ON cases.id = case_contacts.case_id';
        $parameters = [];

        if ($search !== '') {
            $sql .= ' WHERE contacts.name LIKE ? OR contacts.email LIKE ? OR clients.name LIKE ? OR clients.company LIKE ?';
            $parameters = array_fill(0, 4, '%' . $search . '%');
        }

        $sql .= ' GROUP BY contacts.id ORDER BY contacts.name, contacts.id';
        $statement = $this->db->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT contacts.*, clients.name AS client_name, clients.company AS client_company
             FROM contacts LEFT JOIN clients ON clients.id = contacts.client_id WHERE contacts.id = ?'
        );
        $statement->execute([$id]);
        $contact = $statement->fetch();

        return $contact === false ? null : $contact;
    }

    public function byClient(int $clientId): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT * FROM contacts WHERE client_id = ? ORDER BY name, id'
        );
        $statement->execute([$clientId]);

        return $statement->fetchAll();
    }

    public function byCase(int $caseId): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT contacts.*, clients.name AS client_name FROM contacts
             JOIN case_contacts ON case_contacts.contact_id = contacts.id
             LEFT JOIN clients ON clients.id = contacts.client_id
             WHERE case_contacts.case_id = ? ORDER BY contacts.name, contacts.id'
        );
        $statement->execute([$caseId]);

        return $statement->fetchAll();
    }

    public function create(array $data): array
    {
        $now = date('Y-m-d H:i:s');
        $statement = $this->db->pdo->prepare(
            'INSERT INTO contacts (client_id, name, job_title, email, phone, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $data['client_id'], $data['name'], $data['job_title'], $data['email'],
            $data['phone'], $data['notes'], $now, $now,
        ]);

        return $this->find((int) $this->db->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $statement = $this->db->pdo->prepare(
            'UPDATE contacts SET client_id = ?, name = ?, job_title = ?, email = ?, phone = ?, notes = ?, updated_at = ?
             WHERE id = ?'
        );
        $statement->execute([
            $data['client_id'], $data['name'], $data['job_title'], $data['email'],
            $data['phone'], $data['notes'], date('Y-m-d H:i:s'), $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->pdo->prepare('DELETE FROM contacts WHERE id = ?');
        $statement->execute([$id]);
    }
}
