<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\CaseRepository;
use Crm\Repositories\ClientRepository;
use Crm\Repositories\ContactRepository;
use Crm\Repositories\UserRepository;

final class CaseController extends Controller
{
    private CaseRepository $cases;
    private ClientRepository $clients;
    private ContactRepository $contacts;
    private UserRepository $users;

    public function __construct(\Crm\Core\App $app)
    {
        parent::__construct($app);
        $this->cases = new CaseRepository($app->db);
        $this->clients = new ClientRepository($app->db);
        $this->contacts = new ContactRepository($app->db);
        $this->users = new UserRepository($app->db);
    }

    public function index(): string
    {
        $status = (string) ($_GET['status'] ?? '');
        $kind = (string) ($_GET['kind'] ?? '');

        return $this->app->render('cases/index', [
            'title' => 'Cases',
            'cases' => $this->cases->all($status, $kind),
            'statuses' => CaseRepository::STATUSES,
            'types' => CaseRepository::TYPES,
            'activeStatus' => $status,
            'activeType' => $kind,
        ]);
    }

    public function create(): string
    {
        return $this->app->render('cases/form', [
            'title' => 'New case',
            'case' => null,
            'clients' => $this->clients->all(),
            'contacts' => $this->contacts->all(),
            'users' => $this->users->assignable(),
            'statuses' => CaseRepository::STATUSES,
            'types' => CaseRepository::TYPES,
            'priorities' => CaseRepository::PRIORITIES,
            'action' => '/cases',
            'selectedClient' => (int) ($_GET['client_id'] ?? 0),
            'selectedContacts' => [],
        ]);
    }

    public function store(): never
    {
        $case = $this->cases->create($this->validated());
        $this->app->hooks->dispatch('case.created', $case);
        flash('success', 'Case created.');
        redirect('/cases');
    }

    public function edit(string $id): string
    {
        $case = $this->cases->find((int) $id);
        if ($case === null) {
            return $this->notFound();
        }

        return $this->app->render('cases/form', [
            'title' => 'Edit case #' . $case['id'],
            'case' => $case,
            'clients' => $this->clients->all(),
            'contacts' => $this->contacts->all(),
            'users' => $this->users->assignable(),
            'statuses' => CaseRepository::STATUSES,
            'types' => CaseRepository::TYPES,
            'priorities' => CaseRepository::PRIORITIES,
            'action' => '/cases/' . $id . '/update',
            'selectedClient' => (int) $case['client_id'],
            'selectedContacts' => $case['contact_ids'],
        ]);
    }

    public function update(string $id): never
    {
        $case = $this->cases->find((int) $id);
        if ($case === null) {
            flash('error', 'Case not found.');
            redirect('/cases');
        }

        $updated = $this->cases->update((int) $id, $this->validated());
        $this->dispatchUpdateEvents($case, $updated);
        flash('success', 'Case updated.');
        redirect('/cases');
    }

    public function updateStatus(string $id): never
    {
        $case = $this->cases->find((int) $id);
        $status = (string) ($_POST['status'] ?? '');

        if ($case === null || !isset(CaseRepository::STATUSES[$status])) {
            flash('error', 'Could not change the case status.');
            redirect('/cases');
        }

        $updated = $this->cases->updateStatus((int) $id, $status);
        $this->dispatchUpdateEvents($case, $updated);
        flash('success', 'Case status changed.');
        redirect('/cases');
    }

    public function delete(string $id): never
    {
        $case = $this->cases->find((int) $id);
        if ($case !== null) {
            $this->cases->delete((int) $id);
            $this->app->hooks->dispatch('case.deleted', $case);
            flash('success', 'Case deleted.');
        }

        redirect('/cases');
    }

    private function dispatchUpdateEvents(array $before, array $after): void
    {
        $this->app->hooks->dispatch('case.updated', $after, $before);

        if ($before['status'] !== $after['status']) {
            $this->app->hooks->dispatch('case.status_changed', $after, $before['status'], $after['status']);
        }
    }

    private function validated(): array
    {
        $amountInput = str_replace(',', '.', (string) ($_POST['amount'] ?? '0'));
        $contactIds = $_POST['contact_ids'] ?? [];
        $contactIds = is_array($contactIds) ? $contactIds : [];
        $contactIds = array_values(array_unique(array_filter(array_map('intval', $contactIds))));

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0) ?: null,
            'assignee_id' => (int) ($_POST['assignee_id'] ?? 0) ?: null,
            'title' => trim((string) ($_POST['title'] ?? '')),
            'kind' => (string) ($_POST['kind'] ?? 'issue'),
            'status' => (string) ($_POST['status'] ?? 'new'),
            'priority' => (string) ($_POST['priority'] ?? 'normal'),
            'due_date' => trim((string) ($_POST['due_date'] ?? '')),
            'amount' => (float) $amountInput,
            'description' => trim((string) ($_POST['description'] ?? '')),
            'contact_ids' => $contactIds,
        ];

        $date = \DateTime::createFromFormat('Y-m-d', $data['due_date']);
        $validDate = $data['due_date'] === ''
            || ($date !== false && $date->format('Y-m-d') === $data['due_date']);
        $validContacts = true;
        foreach ($contactIds as $contactId) {
            if ($this->contacts->find($contactId) === null) {
                $validContacts = false;
                break;
            }
        }

        if ($data['title'] === ''
            || ($data['client_id'] !== null && $this->clients->find($data['client_id']) === null)
            || ($data['assignee_id'] !== null && $this->users->find($data['assignee_id']) === null)
            || !isset(CaseRepository::TYPES[$data['kind']], CaseRepository::STATUSES[$data['status']], CaseRepository::PRIORITIES[$data['priority']])
            || !is_numeric($amountInput) || $data['amount'] < 0 || !$validDate || !$validContacts) {
            flash('error', 'Check the title, type, status, assignee, links, amount and due date.');
            $id = (int) ($_POST['id'] ?? 0);
            redirect('/cases' . ($id > 0 ? '/' . $id . '/edit' : '/create'));
        }

        if ($data['due_date'] === '') {
            $data['due_date'] = null;
        }

        return $data;
    }
}
