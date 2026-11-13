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
    }
}
