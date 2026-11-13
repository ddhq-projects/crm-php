<?php

declare(strict_types=1);

namespace Crm\Repositories;

use Crm\Core\Database;
use PDO;

final class DealRepository
{
    public const STAGES = [
        'new' => 'Новая',
        'qualified' => 'Квалификация',
        'proposal' => 'Предложение',
        'negotiation' => 'Переговоры',
        'won' => 'Выиграна',
        'lost' => 'Проиграна',
    ];

    public function __construct(private readonly Database $db)
    {
    }

    public function all(string $stage = ''): array
    {
        $sql = 'SELECT deals.*, clients.name AS client_name, clients.company AS client_company
                FROM deals JOIN clients ON clients.id = deals.client_id';
        $parameters = [];

        if (isset(self::STAGES[$stage])) {
            $sql .= ' WHERE deals.stage = ?';
            $parameters[] = $stage;
        }

        $sql .= ' ORDER BY deals.created_at DESC, deals.id DESC';
        $statement = $this->db->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT deals.*, clients.name AS client_name, clients.company AS client_company
             FROM deals JOIN clients ON clients.id = deals.client_id WHERE deals.id = ?'
        );
        $statement->execute([$id]);
        $deal = $statement->fetch();

        return $deal === false ? null : $deal;
    }

    public function byClient(int $clientId): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT * FROM deals WHERE client_id = ? ORDER BY created_at DESC, id DESC'
        );
        $statement->execute([$clientId]);

        return $statement->fetchAll();
    }

    public function recent(int $limit = 6): array
    {
        $statement = $this->db->pdo->prepare(
            'SELECT deals.*, clients.name AS client_name, clients.company AS client_company
             FROM deals JOIN clients ON clients.id = deals.client_id
             ORDER BY deals.updated_at DESC, deals.id DESC LIMIT ?'
        );
        $statement->bindValue(1, $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function pipeline(): array
    {
        $result = [];
        foreach (self::STAGES as $key => $label) {
            $result[$key] = ['label' => $label, 'count' => 0, 'amount' => 0];
        }

        $rows = $this->db->pdo->query(
            'SELECT stage, COUNT(*) AS count, SUM(amount) AS amount FROM deals GROUP BY stage'
        )->fetchAll();
        foreach ($rows as $row) {
            if (isset($result[$row['stage']])) {
                $result[$row['stage']]['count'] = (int) $row['count'];
                $result[$row['stage']]['amount'] = (float) $row['amount'];
            }
        }

        return $result;
    }

    public function stats(): array
    {
        return $this->db->pdo->query(
            "SELECT COUNT(*) AS total,
             COALESCE(SUM(CASE WHEN stage NOT IN ('won', 'lost') THEN amount END), 0) AS open_amount,
             COALESCE(SUM(CASE WHEN stage = 'won' THEN amount END), 0) AS won_amount
             FROM deals"
        )->fetch();
    }

    public function create(array $data): array
    {
        $now = date('Y-m-d H:i:s');
        $statement = $this->db->pdo->prepare(
            'INSERT INTO deals (client_id, title, amount, stage, expected_close_date, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $data['client_id'], $data['title'], $data['amount'], $data['stage'],
            $data['expected_close_date'], $data['notes'], $now, $now,
        ]);

        return $this->find((int) $this->db->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $statement = $this->db->pdo->prepare(
            'UPDATE deals SET client_id = ?, title = ?, amount = ?, stage = ?, expected_close_date = ?, notes = ?, updated_at = ?
             WHERE id = ?'
        );
        $statement->execute([
            $data['client_id'], $data['title'], $data['amount'], $data['stage'],
            $data['expected_close_date'], $data['notes'], date('Y-m-d H:i:s'), $id,
        ]);

        return $this->find($id);
    }

    public function updateStage(int $id, string $stage): array
    {
        $statement = $this->db->pdo->prepare('UPDATE deals SET stage = ?, updated_at = ? WHERE id = ?');
        $statement->execute([$stage, date('Y-m-d H:i:s'), $id]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->pdo->prepare('DELETE FROM deals WHERE id = ?');
        $statement->execute([$id]);
    }
}
