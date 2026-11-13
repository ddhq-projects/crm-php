<?php

declare(strict_types=1);

use Crm\Controllers\ClientController;
use Crm\Controllers\DashboardController;
use Crm\Controllers\DealController;
use Crm\Controllers\PluginController;

$dashboard = new DashboardController($app);
$clients = new ClientController($app);
$deals = new DealController($app);
$plugins = new PluginController($app);

$app->router->get('/', [$dashboard, 'index']);

$app->router->get('/clients', [$clients, 'index']);
$app->router->get('/clients/create', [$clients, 'create']);
$app->router->post('/clients', [$clients, 'store']);
$app->router->get('/clients/{id}', [$clients, 'show']);
$app->router->get('/clients/{id}/edit', [$clients, 'edit']);
$app->router->post('/clients/{id}/update', [$clients, 'update']);
$app->router->post('/clients/{id}/delete', [$clients, 'delete']);

$app->router->get('/deals', [$deals, 'index']);
$app->router->get('/deals/create', [$deals, 'create']);
$app->router->post('/deals', [$deals, 'store']);
$app->router->get('/deals/{id}/edit', [$deals, 'edit']);
$app->router->post('/deals/{id}/update', [$deals, 'update']);
$app->router->post('/deals/{id}/stage', [$deals, 'updateStage']);
$app->router->post('/deals/{id}/delete', [$deals, 'delete']);

$app->router->get('/plugins', [$plugins, 'index']);
$app->router->post('/plugins/toggle', [$plugins, 'toggle']);
