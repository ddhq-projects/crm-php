<?php

declare(strict_types=1);

namespace Crm\Controllers;

final class PluginController extends Controller
{
    public function index(): string
    {
        return $this->app->render('plugins/index', [
            'title' => 'Plugins',
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
                flash('success', 'Plugin enabled. Its hooks are active from the next request.');
            } elseif ($action === 'disable') {
                $this->app->plugins->disable($slug);
                flash('success', 'Plugin disabled.');
            } else {
                flash('error', 'Unknown action.');
            }
        } catch (\Throwable) {
            flash('error', 'Could not change the plugin state.');
        }

        redirect('/plugins');
    }
}
