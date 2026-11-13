<?php

declare(strict_types=1);

namespace Crm\Repositories;

use Crm\Core\Database;

final class UserRepository
{
    public const ROLES = [
        'admin' => 'Administrator',
        'user' => 'User',
        'client' => 'Client',
    ];

    public function __construct(private readonly Database $db)
    {
    }

    public function all(): array
    {
        return $this->db->pdo->query(
            'SELECT users.*, COUNT(cases.id) AS assigned_cases_count
             FROM users LEFT JOIN cases ON cases.assignee_id = users.id
             GROUP BY users.id ORDER BY users.name, users.id'
        )->fetchAll();
    }

    public function assignable(): array
    {
        return $this->db->pdo->query(
            "SELECT * FROM users WHERE active = 1 AND role != 'client' ORDER BY name, id"
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $statement->execute([$id]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $statement = $this->db->pdo->prepare('SELECT 1 FROM users WHERE email = ? AND id != ?');
        $statement->execute([$email, $exceptId]);

        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): array
    {
        $now = date('Y-m-d H:i:s');
        $statement = $this->db->pdo->prepare(
            'INSERT INTO users (name, email, role, active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([$data['name'], $data['email'], $data['role'], $data['active'], $now, $now]);

        return $this->find((int) $this->db->pdo->lastInsertId());
    }

    public function update(int $id, array $data): array
    {
        $statement = $this->db->pdo->prepare(
            'UPDATE users SET name = ?, email = ?, role = ?, active = ?, updated_at = ? WHERE id = ?'
        );
        $statement->execute([$data['name'], $data['email'], $data['role'], $data['active'], date('Y-m-d H:i:s'), $id]);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->pdo->prepare('DELETE FROM users WHERE id = ?');
        $statement->execute([$id]);
    }
}
