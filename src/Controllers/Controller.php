<?php

declare(strict_types=1);

namespace Crm\Controllers;

use Crm\Core\App;

abstract class Controller
{
    public function __construct(protected readonly App $app)
    {
    }

    protected function notFound(): string
    {
        http_response_code(404);

        return $this->app->render('errors/404', ['title' => 'Not found']);
    }
}
