<?php

declare(strict_types=1);

use Crm\Controllers\CaseController;
use Crm\Controllers\ClientController;
use Crm\Controllers\ContactController;
use Crm\Controllers\DashboardController;
use Crm\Controllers\PluginController;
use Crm\Controllers\UserController;

$dashboard = new DashboardController($app);
$cases = new CaseController($app);
$clients = new ClientController($app);
$contacts = new ContactController($app);
$users = new UserController($app);
$plugins = new PluginController($app);

$app->router->get('/', [$dashboard, 'index']);

$app->router->get('/cases', [$cases, 'index']);
$app->router->get('/cases/create', [$cases, 'create']);
$app->router->post('/cases', [$cases, 'store']);
$app->router->get('/cases/{id}/edit', [$cases, 'edit']);
$app->router->post('/cases/{id}/update', [$cases, 'update']);
$app->router->post('/cases/{id}/status', [$cases, 'updateStatus']);
$app->router->post('/cases/{id}/delete', [$cases, 'delete']);

$app->router->get('/clients', [$clients, 'index']);
$app->router->get('/clients/create', [$clients, 'create']);
$app->router->post('/clients', [$clients, 'store']);
$app->router->get('/clients/{id}', [$clients, 'show']);
$app->router->get('/clients/{id}/edit', [$clients, 'edit']);
$app->router->post('/clients/{id}/update', [$clients, 'update']);
$app->router->post('/clients/{id}/delete', [$clients, 'delete']);

$app->router->get('/contacts', [$contacts, 'index']);
$app->router->get('/contacts/create', [$contacts, 'create']);
$app->router->post('/contacts', [$contacts, 'store']);
$app->router->get('/contacts/{id}/edit', [$contacts, 'edit']);
$app->router->post('/contacts/{id}/update', [$contacts, 'update']);
$app->router->post('/contacts/{id}/delete', [$contacts, 'delete']);

$app->router->get('/users', [$users, 'index']);
$app->router->get('/users/create', [$users, 'create']);
$app->router->post('/users', [$users, 'store']);
$app->router->get('/users/{id}/edit', [$users, 'edit']);
$app->router->post('/users/{id}/update', [$users, 'update']);
$app->router->post('/users/{id}/delete', [$users, 'delete']);

$app->router->get('/deals', fn (): never => redirect('/cases'));
$app->router->get('/deals/create', fn (): never => redirect('/cases/create'));
$app->router->get('/deals/{id}/edit', fn (string $id): never => redirect('/cases/' . $id . '/edit'));

$app->router->get('/plugins', [$plugins, 'index']);
$app->router->post('/plugins/toggle', [$plugins, 'toggle']);
