<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Repositories\UserRepository;

final class UserController extends Controller
{
    private UserRepository $users;

    public function __construct(\Crm\Core\App $app)
    {
        parent::__construct($app);
        $this->users = new UserRepository($app->db);
    }

    public function index(): string
    {
        return $this->app->render('users/index', [
            'title' => 'Users',
            'users' => $this->users->all(),
            'roles' => UserRepository::ROLES,
        ]);
    }

    public function create(): string
    {
        return $this->app->render('users/form', [
            'title' => 'New user',
            'user' => null,
            'roles' => UserRepository::ROLES,
            'action' => '/users',
        ]);
    }

    public function store(): never
    {
        $user = $this->users->create($this->validated());
        $this->app->hooks->dispatch('user.created', $user);
        flash('success', 'User created.');
        redirect('/users');
    }

    public function edit(string $id): string
    {
        $user = $this->users->find((int) $id);
        if ($user === null) {
            return $this->notFound();
        }

        return $this->app->render('users/form', [
            'title' => 'Edit user',
            'user' => $user,
            'roles' => UserRepository::ROLES,
            'action' => '/users/' . $id . '/update',
        ]);
    }

    public function update(string $id): never
    {
        $user = $this->users->find((int) $id);
        if ($user === null) {
            flash('error', 'User not found.');
            redirect('/users');
        }

        $updated = $this->users->update((int) $id, $this->validated());
        $this->app->hooks->dispatch('user.updated', $updated, $user);
        flash('success', 'User updated.');
        redirect('/users');
    }

    public function delete(string $id): never
    {
        $user = $this->users->find((int) $id);
        if ($user !== null) {
            $this->users->delete((int) $id);
            $this->app->hooks->dispatch('user.deleted', $user);
            flash('success', 'User deleted. Assigned cases are now unassigned.');
        }

        redirect('/users');
    }

    private function validated(): array
    {
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
            'role' => (string) ($_POST['role'] ?? 'user'),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];
        $id = (int) ($_POST['id'] ?? 0);

        if ($data['name'] === '' || filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
            || !isset(UserRepository::ROLES[$data['role']]) || $this->users->emailExists($data['email'], $id)) {
            flash('error', 'Check the name, unique email address and role.');
            redirect('/users' . ($id > 0 ? '/' . $id . '/edit' : '/create'));
        }

        return $data;
    }
}
