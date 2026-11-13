<?php

declare(strict_types=1);

namespace Crm\Core;

interface PluginInterface
{
    public function register(App $app): void;

    public function activate(App $app): void;

    public function deactivate(App $app): void;
}
