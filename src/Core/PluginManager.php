<?php

declare(strict_types=1);

namespace Crm\Core;

use RuntimeException;

final class PluginManager
{
    private array $plugins = [];
    private array $instances = [];
    private array $states = [];

    public function __construct(private readonly App $app)
    {
        $this->discover();
    }

    public function loadEnabled(): void
    {
        foreach ($this->plugins as $slug => $manifest) {
            if ($this->isEnabled($slug)) {
                $this->instance($slug)->register($this->app);
            }
        }
    }

    public function all(): array
    {
        $result = [];
        foreach ($this->plugins as $slug => $manifest) {
            $result[] = $manifest + [
                'enabled' => $this->isEnabled($slug),
                'loaded' => isset($this->instances[$slug]),
            ];
        }

        return $result;
    }

    public function enable(string $slug): void
    {
        $plugin = $this->instance($slug);
        $this->setEnabled($slug, true);

        try {
            $plugin->activate($this->app);
        } catch (\Throwable $exception) {
            $this->setEnabled($slug, false);
            throw $exception;
        }
    }

    public function disable(string $slug): void
    {
        $plugin = $this->instance($slug);
        $plugin->deactivate($this->app);
        $this->setEnabled($slug, false);
    }

    private function discover(): void
    {
        $manifestFiles = glob($this->app->basePath . '/plugins/*/plugin.php') ?: [];
        sort($manifestFiles);

        foreach ($manifestFiles as $file) {
            $manifest = require $file;
            $slug = $manifest['slug'] ?? null;

            if (!is_string($slug) || !preg_match('/^[a-z0-9-]+$/', $slug)) {
                throw new RuntimeException('Некорректный slug плагина: ' . $file);
            }

            $manifest['path'] = dirname($file);
            $this->plugins[$slug] = $manifest;

            $statement = $this->app->db->pdo->prepare(
                'INSERT OR IGNORE INTO plugins (slug, enabled, updated_at) VALUES (?, ?, ?)'
            );
            $statement->execute([$slug, !empty($manifest['default_enabled']) ? 1 : 0, date('Y-m-d H:i:s')]);

            if ($statement->rowCount() === 1 && !empty($manifest['default_enabled'])) {
                $this->instance($slug)->activate($this->app);
            }
        }
    }

    private function instance(string $slug): PluginInterface
    {
        if (isset($this->instances[$slug])) {
            return $this->instances[$slug];
        }

        $manifest = $this->plugins[$slug] ?? null;
        if ($manifest === null) {
            throw new RuntimeException('Плагин не найден.');
        }

        require_once $manifest['path'] . '/' . $manifest['class_file'];
        $class = $manifest['class'];
        $plugin = new $class();

        if (!$plugin instanceof PluginInterface) {
            throw new RuntimeException('Плагин должен реализовывать PluginInterface.');
        }

        return $this->instances[$slug] = $plugin;
    }

    private function isEnabled(string $slug): bool
    {
        if (!array_key_exists($slug, $this->states)) {
            $statement = $this->app->db->pdo->prepare('SELECT enabled FROM plugins WHERE slug = ?');
            $statement->execute([$slug]);
            $this->states[$slug] = (bool) $statement->fetchColumn();
        }

        return $this->states[$slug];
    }

    private function setEnabled(string $slug, bool $enabled): void
    {
        $statement = $this->app->db->pdo->prepare(
            'UPDATE plugins SET enabled = ?, updated_at = ? WHERE slug = ?'
        );
        $statement->execute([$enabled ? 1 : 0, date('Y-m-d H:i:s'), $slug]);
        $this->states[$slug] = $enabled;
    }
}
