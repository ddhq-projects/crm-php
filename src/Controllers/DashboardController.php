<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\CaseRepository;

final class DashboardController extends Controller
{
    public function index(): string
    {
        $cases = new CaseRepository($this->app->db);

        return $this->app->render('dashboard', [
            'title' => 'Dashboard',
            'clientCount' => (int) $this->app->db->pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
            'userCount' => (int) $this->app->db->pdo->query('SELECT COUNT(*) FROM users WHERE active = 1')->fetchColumn(),
            'caseStats' => $cases->stats(),
            'statusSummary' => $cases->statusSummary(),
            'recentCases' => $cases->recent(),
            'widgets' => $this->app->hooks->applyFilters('dashboard.widgets', [], $this->app),
        ]);
    }
}
