<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\ClientRepository;
use Crm\Repositories\ContactRepository;

final class ContactController extends Controller
{
    private ContactRepository $contacts;
    private ClientRepository $clients;

    public function __construct(\Crm\Core\App $app)
    {
        parent::__construct($app);
        $this->contacts = new ContactRepository($app->db);
        $this->clients = new ClientRepository($app->db);
    }

    public function index(): string
    {
        $search = trim((string) ($_GET['search'] ?? ''));

        return $this->app->render('contacts/index', [
            'title' => 'Contacts',
            'contacts' => $this->contacts->all($search),
            'search' => $search,
        ]);
    }

    public function create(): string
    {
        return $this->app->render('contacts/form', [
            'title' => 'New contact',
            'contact' => null,
            'clients' => $this->clients->all(),
            'action' => '/contacts',
            'selectedClient' => (int) ($_GET['client_id'] ?? 0),
        ]);
    }

    public function store(): never
    {
        $contact = $this->contacts->create($this->validated());
        $this->app->hooks->dispatch('contact.created', $contact);
        flash('success', 'Contact created.');
        redirect('/contacts');
    }

    public function edit(string $id): string
    {
        $contact = $this->contacts->find((int) $id);
        if ($contact === null) {
            return $this->notFound();
        }

        return $this->app->render('contacts/form', [
            'title' => 'Edit contact',
            'contact' => $contact,
            'clients' => $this->clients->all(),
            'action' => '/contacts/' . $id . '/update',
            'selectedClient' => (int) $contact['client_id'],
        ]);
    }

    public function update(string $id): never
    {
        $contact = $this->contacts->find((int) $id);
        if ($contact === null) {
            flash('error', 'Contact not found.');
            redirect('/contacts');
        }

        $updated = $this->contacts->update((int) $id, $this->validated());
        $this->app->hooks->dispatch('contact.updated', $updated, $contact);
        flash('success', 'Contact updated.');
        redirect('/contacts');
    }

    public function delete(string $id): never
    {
        $contact = $this->contacts->find((int) $id);
        if ($contact !== null) {
            $this->contacts->delete((int) $id);
            $this->app->hooks->dispatch('contact.deleted', $contact);
            flash('success', 'Contact deleted. Case links were removed.');
        }

        redirect('/contacts');
    }

    private function validated(): array
    {
        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0) ?: null,
            'name' => trim((string) ($_POST['name'] ?? '')),
            'job_title' => trim((string) ($_POST['job_title'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];
        $id = (int) ($_POST['id'] ?? 0);

        if ($data['name'] === '' || ($data['client_id'] !== null && $this->clients->find($data['client_id']) === null)
            || ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false)) {
            flash('error', 'Check the contact name, client and email address.');
            redirect('/contacts' . ($id > 0 ? '/' . $id . '/edit' : '/create'));
        }

        return $data;
    }
}
