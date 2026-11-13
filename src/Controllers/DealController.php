<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\ClientRepository;
use Crm\Repositories\DealRepository;

final class DealController extends Controller
{
    private DealRepository $deals;
    private ClientRepository $clients;

    public function __construct(\Crm\Core\App $app)
    {
        parent::__construct($app);
        $this->deals = new DealRepository($app->db);
        $this->clients = new ClientRepository($app->db);
    }

    public function index(): string
    {
        $stage = (string) ($_GET['stage'] ?? '');

        return $this->app->render('deals/index', [
            'title' => 'Сделки',
            'deals' => $this->deals->all($stage),
            'stages' => DealRepository::STAGES,
            'activeStage' => $stage,
        ]);
    }

    public function create(): string
    {
        return $this->app->render('deals/form', [
            'title' => 'Новая сделка',
            'deal' => null,
            'clients' => $this->clients->all(),
            'stages' => DealRepository::STAGES,
            'action' => '/deals',
            'selectedClient' => (int) ($_GET['client_id'] ?? 0),
        ]);
    }

    public function store(): never
    {
        $deal = $this->deals->create($this->validated());
        $this->app->hooks->dispatch('deal.created', $deal);
        flash('success', 'Сделка создана.');
        redirect('/deals');
    }

    public function edit(string $id): string
    {
        $deal = $this->deals->find((int) $id);
        if ($deal === null) {
            return $this->notFound();
        }

        return $this->app->render('deals/form', [
            'title' => 'Редактировать сделку',
            'deal' => $deal,
            'clients' => $this->clients->all(),
            'stages' => DealRepository::STAGES,
            'action' => '/deals/' . $id . '/update',
            'selectedClient' => (int) $deal['client_id'],
        ]);
    }

    public function update(string $id): never
    {
        $deal = $this->deals->find((int) $id);
        if ($deal === null) {
            flash('error', 'Сделка не найдена.');
            redirect('/deals');
        }

        $updated = $this->deals->update((int) $id, $this->validated());
        $this->dispatchUpdateEvents($deal, $updated);
        flash('success', 'Сделка обновлена.');
        redirect('/deals');
    }

    public function updateStage(string $id): never
    {
        $deal = $this->deals->find((int) $id);
        $stage = (string) ($_POST['stage'] ?? '');

        if ($deal === null || !isset(DealRepository::STAGES[$stage])) {
            flash('error', 'Не удалось изменить этап сделки.');
            redirect('/deals');
        }

        $updated = $this->deals->updateStage((int) $id, $stage);
        $this->dispatchUpdateEvents($deal, $updated);
        flash('success', 'Этап сделки изменён.');
        redirect('/deals');
    }

    public function delete(string $id): never
    {
        $deal = $this->deals->find((int) $id);
        if ($deal !== null) {
            $this->deals->delete((int) $id);
            $this->app->hooks->dispatch('deal.deleted', $deal);
            flash('success', 'Сделка удалена.');
        }

        redirect('/deals');
    }

    private function dispatchUpdateEvents(array $before, array $after): void
    {
        $this->app->hooks->dispatch('deal.updated', $after, $before);

        if ($before['stage'] !== $after['stage']) {
            $this->app->hooks->dispatch('deal.stage_changed', $after, $before['stage'], $after['stage']);
        }
    }

    private function validated(): array
    {
        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'amount' => (float) str_replace(',', '.', (string) ($_POST['amount'] ?? '0')),
            'stage' => (string) ($_POST['stage'] ?? 'new'),
            'expected_close_date' => trim((string) ($_POST['expected_close_date'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];

        $amountInput = str_replace(',', '.', (string) ($_POST['amount'] ?? '0'));
        $date = \DateTime::createFromFormat('Y-m-d', $data['expected_close_date']);
        $validDate = $data['expected_close_date'] === ''
            || ($date !== false && $date->format('Y-m-d') === $data['expected_close_date']);

        if ($data['title'] === '' || $this->clients->find($data['client_id']) === null
            || !is_numeric($amountInput) || $data['amount'] < 0
            || !isset(DealRepository::STAGES[$data['stage']]) || !$validDate) {
            flash('error', 'Проверьте клиента, название, сумму, этап и дату сделки.');
            redirect('/deals/create');
        }

        if ($data['expected_close_date'] === '') {
            $data['expected_close_date'] = null;
        }

        return $data;
    }
}
