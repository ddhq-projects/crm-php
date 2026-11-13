<?php

declare(strict_types=1);

namespace DealActivity;

use Crm\Core\App;
use Crm\Core\PluginInterface;
use PDO;

final class DealActivityPlugin implements PluginInterface
{
    public function register(App $app): void
    {
        $app->hooks->listen('client.created', function (array $client) use ($app): void {
            $this->log($app, 'client.created', 'client', (int) $client['id'], 'Added client "' . $client['name'] . '"');
        });

        $app->hooks->listen('deal.created', function (array $deal) use ($app): void {
            $this->log($app, 'deal.created', 'deal', (int) $deal['id'], 'Created deal "' . $deal['title'] . '" for ' . money($deal['amount']));
        });

        $app->hooks->listen('deal.stage_changed', function (array $deal, string $from, string $to) use ($app): void {
            $stages = \Crm\Repositories\DealRepository::STAGES;
            $message = 'Deal "' . $deal['title'] . '": ' . $stages[$from] . ' → ' . $stages[$to];
            $this->log($app, 'deal.stage_changed', 'deal', (int) $deal['id'], $message);
        }, 20);

        $app->hooks->listen('deal.deleted', function (array $deal) use ($app): void {
            $this->log($app, 'deal.deleted', 'deal', (int) $deal['id'], 'Deleted deal "' . $deal['title'] . '"');
        });

        $app->hooks->addFilter('admin.navigation', function (array $navigation): array {
            $navigation[] = ['url' => '/activity', 'label' => 'Activity'];

            return $navigation;
        });

        $app->hooks->addFilter('dashboard.widgets', function (array $widgets) use ($app): array {
            $widgets[] = [
                'title' => 'Recent activity',
                'url' => '/activity',
                'items' => $app->db->pdo->query(
                    'SELECT message, created_at FROM deal_activity ORDER BY id DESC LIMIT 5'
                )->fetchAll(),
            ];

            return $widgets;
        });

        $app->router->get('/activity', fn (): string => $this->page($app));
    }

    public function activate(App $app): void
    {
        $app->db->pdo->exec(
            'CREATE TABLE IF NOT EXISTS deal_activity (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                event TEXT NOT NULL,
                entity_type TEXT NOT NULL,
                entity_id INTEGER NOT NULL,
                message TEXT NOT NULL,
                created_at TEXT NOT NULL
            )'
        );
        $app->db->pdo->exec(
            'CREATE INDEX IF NOT EXISTS idx_deal_activity_created_at ON deal_activity(created_at)'
        );
    }

    public function deactivate(App $app): void
    {
        // Log data is kept and becomes available again when the plugin is re-enabled.
    }

    private function log(App $app, string $event, string $entityType, int $entityId, string $message): void
    {
        $statement = $app->db->pdo->prepare(
            'INSERT INTO deal_activity (event, entity_type, entity_id, message, created_at) VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([$event, $entityType, $entityId, $message, date('Y-m-d H:i:s')]);
    }

    private function page(App $app): string
    {
        $statement = $app->db->pdo->prepare('SELECT * FROM deal_activity ORDER BY id DESC LIMIT ?');
        $statement->bindValue(1, 200, PDO::PARAM_INT);
        $statement->execute();

        return $app->render(__DIR__ . '/views/activity.php', [
            'title' => 'Activity',
            'activities' => $statement->fetchAll(),
        ]);
    }
}
