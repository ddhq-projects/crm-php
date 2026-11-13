<?php

declare(strict_types=1);

namespace Crm\Core;

final class App
{
    public readonly Database $db;
    public readonly HookManager $hooks;
    public readonly Router $router;
    public readonly View $view;
    public readonly PluginManager $plugins;

    public function __construct(public readonly string $basePath)
    {
        $this->db = new Database($basePath);
        $this->hooks = new HookManager();
        $this->router = new Router();
        $this->view = new View($basePath);
        $this->plugins = new PluginManager($this);
    }

    public function render(string $template, array $data = []): string
    {
        $navigation = $this->hooks->applyFilters('admin.navigation', [
            ['url' => '/', 'label' => 'Обзор'],
            ['url' => '/clients', 'label' => 'Клиенты'],
            ['url' => '/deals', 'label' => 'Сделки'],
            ['url' => '/plugins', 'label' => 'Плагины'],
        ]);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($template, $data + [
            'navigation' => $navigation,
            'flash' => $flash,
            'currentPath' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
        ]);
    }
}
