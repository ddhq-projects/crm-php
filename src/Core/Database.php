<?php

declare(strict_types=1);

namespace Crm\Core;

use PDO;
use RuntimeException;

final class Database
{
    public readonly PDO $pdo;

    public function __construct(string $basePath)
    {
        $path = getenv('CRM_DATABASE') ?: $basePath . '/storage/crm.sqlite';
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Could not create the database directory.');
        }

        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
        $this->pdo->exec('PRAGMA busy_timeout = 5000');

        $schema = file_get_contents($basePath . '/database/schema.sql');
        if ($schema === false) {
            throw new RuntimeException('Database schema not found.');
        }

        $this->pdo->exec($schema);
        $this->migrateLegacyDeals();
    }

    private function migrateLegacyDeals(): void
    {
        $table = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'deals'"
        )->fetchColumn();
        if ($table === false) {
            return;
        }

        $this->pdo->beginTransaction();
        try {
            $this->pdo->exec(
                "INSERT INTO cases (
                    client_id, title, kind, status, priority, due_date, amount, description, created_at, updated_at
                )
                SELECT client_id, title, 'lead',
                    CASE stage
                        WHEN 'won' THEN 'resolved'
                        WHEN 'lost' THEN 'closed'
                        WHEN 'new' THEN 'new'
                        ELSE 'in_progress'
                    END,
                    'normal', expected_close_date, amount, notes, created_at, updated_at
                FROM deals"
            );
            $this->pdo->exec('DROP TABLE deals');
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
