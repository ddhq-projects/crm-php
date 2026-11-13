<?php

declare(strict_types=1);

namespace Crm\Repositories;

use Crm\Core\Database;

final class ClientRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function all(string $search = ''): array
    {
        $sql = "SELECT clients.*, COUNT(deals.id) AS deals_count,
                COALESCE(SUM(CASE WHEN deals.stage NOT IN ('won', 'lost') THEN deals.amount END), 0) AS open_amount
                FROM clients
                LEFT JOIN deals ON deals.client_id = clients.id";
        $parameters = [];

        if ($search !== '') {
            $sql .= ' WHERE clients.name LIKE ? OR clients.company LIKE ? OR clients.email LIKE ?';
            $parameters = array_fill(0, 3, '%' . $search . '%');
        }

        $sql .= ' GROUP BY clients.id ORDER BY clients.created_at DESC, clients.id DESC';
        $statement = $this->db->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->pdo->prepare('SELECT * FROM clients WHERE id = ?');
        $statement->execute([$id]);
        $client = $statement->fetch();

        return $client === false ? null : $client;
    }

    public function create(array $data): array
    {
        $now = date('Y-m-d H:i:s');
        $statement = $this->db->pdo->prepare(
            'INSERT INTO clients (name, company, email, phone, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $data['name'], $data['company'], $data['email'], $data['phone'], $data['notes'], $now, $now,
        ]);

        return $this->find((int) $this->db->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $statement = $this->db->pdo->prepare(
            'UPDATE clients SET name = ?, company = ?, email = ?, phone = ?, notes = ?, updated_at = ? WHERE id = ?'
        );
        $statement->execute([
            $data['name'], $data['company'], $data['email'], $data['phone'], $data['notes'], date('Y-m-d H:i:s'), $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->pdo->prepare('DELETE FROM clients WHERE id = ?');
        $statement->execute([$id]);
    }
}
