<?php

declare(strict_types=1);

namespace Crm\Controllers;

final class PluginController extends Controller
{
    public function index(): string
    {
        return $this->app->render('plugins/index', [
            'title' => 'Плагины',
            'plugins' => $this->app->plugins->all(),
        ]);
    }

    public function toggle(): never
    {
        $slug = (string) ($_POST['slug'] ?? '');
        $action = (string) ($_POST['action'] ?? '');

        try {
            if ($action === 'enable') {
                $this->app->plugins->enable($slug);
                flash('success', 'Плагин включён. Его хуки будут активны со следующего запроса.');
            } elseif ($action === 'disable') {
                $this->app->plugins->disable($slug);
                flash('success', 'Плагин выключен.');
            } else {
                flash('error', 'Неизвестное действие.');
            }
        } catch (\Throwable) {
            flash('error', 'Не удалось изменить состояние плагина.');
        }

        redirect('/plugins');
    }
}
