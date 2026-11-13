<?php

declare(strict_types=1);

use Crm\Core\App;
use Crm\Core\Csrf;

require dirname(__DIR__) . '/src/autoload.php';

session_name('crm_php_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

try {
    $app = new App(dirname(__DIR__));
    require $app->basePath . '/routes/web.php';
    $app->plugins->loadEnabled();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::validate($_POST['_token'] ?? null)) {
        http_response_code(419);
        echo $app->render('errors/419', ['title' => 'Session expired']);
        exit;
    }

    $response = $app->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
    if ($response === null) {
        http_response_code(404);
        $response = $app->render('errors/404', ['title' => 'Page not found']);
    }

    echo $response;
} catch (Throwable $exception) {
    http_response_code(500);
    $message = getenv('CRM_DEBUG') === 'true' ? $exception->getMessage() : 'Check the server logs.';
    error_log((string) $exception);

    if (isset($app)) {
        echo $app->render('errors/500', ['title' => 'Error', 'message' => $message]);
    } else {
        echo 'CRM PHP failed to start: ' . e($message);
    }
}
