<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\ClientRepository;
use Crm\Repositories\DealRepository;

final class DashboardController extends Controller
{
    public function index(): string
    {
        $clients = new ClientRepository($this->app->db);
        $deals = new DealRepository($this->app->db);

        return $this->app->render('dashboard', [
            'title' => 'Обзор',
            'clientCount' => (int) $this->app->db->pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
            'dealStats' => $deals->stats(),
            'pipeline' => $deals->pipeline(),
            'recentDeals' => $deals->recent(),
            'widgets' => $this->app->hooks->applyFilters('dashboard.widgets', [], $this->app),
        ]);
    }
}
