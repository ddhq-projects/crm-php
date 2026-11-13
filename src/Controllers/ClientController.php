<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\ClientRepository;
use Crm\Repositories\DealRepository;

final class ClientController extends Controller
{
    private ClientRepository $clients;

    public function __construct(\Crm\Core\App $app)
    {
        parent::__construct($app);
        $this->clients = new ClientRepository($app->db);
    }

    public function index(): string
    {
        $search = trim((string) ($_GET['search'] ?? ''));

        return $this->app->render('clients/index', [
            'title' => 'Клиенты',
            'clients' => $this->clients->all($search),
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        return $this->app->render('clients/form', [
            'title' => 'Новый клиент',
            'client' => null,
            'action' => '/clients',
        ]);
    }

    public function store(): never
    {
        $client = $this->clients->create($this->validated());
        $this->app->hooks->dispatch('client.created', $client);
        flash('success', 'Клиент добавлен.');
        redirect('/clients/' . $client['id']);
    }

    public function show(string $id): string
    {
        $client = $this->clients->find((int) $id);
        if ($client === null) {
            return $this->notFound();
        }

        return $this->app->render('clients/show', [
            'title' => $client['name'],
            'client' => $client,
            'deals' => (new DealRepository($this->app->db))->byClient((int) $id),
            'stages' => DealRepository::STAGES,
        ]);
    }

    public function edit(string $id): string
    {
        $client = $this->clients->find((int) $id);
        if ($client === null) {
            return $this->notFound();
        }

        return $this->app->render('clients/form', [
            'title' => 'Редактировать клиента',
            'client' => $client,
            'action' => '/clients/' . $id . '/update',
        ]);
    }

    public function update(string $id): never
    {
        $client = $this->clients->find((int) $id);
        if ($client === null) {
            flash('error', 'Клиент не найден.');
            redirect('/clients');
        }

        $updated = $this->clients->update((int) $id, $this->validated());
        $this->app->hooks->dispatch('client.updated', $updated, $client);
        flash('success', 'Клиент обновлён.');
        redirect('/clients/' . $id);
    }

    public function delete(string $id): never
    {
        $client = $this->clients->find((int) $id);
        if ($client !== null) {
            $this->clients->delete((int) $id);
            $this->app->hooks->dispatch('client.deleted', $client);
            flash('success', 'Клиент и связанные сделки удалены.');
        }

        redirect('/clients');
    }

    private function validated(): array
    {
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'company' => trim((string) ($_POST['company'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];

        if ($data['name'] === '') {
            flash('error', 'Укажите имя клиента.');
            redirect('/clients' . (isset($_POST['id']) ? '/' . (int) $_POST['id'] . '/edit' : '/create'));
        }

        if ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            flash('error', 'Проверьте адрес электронной почты.');
            redirect('/clients' . (isset($_POST['id']) ? '/' . (int) $_POST['id'] . '/edit' : '/create'));
        }

        return $data;
    }
}
