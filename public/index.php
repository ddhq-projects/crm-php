<?php

declare(strict_types=1);

use Crm\Core\App;
use Crm\Core\Csrf;

require dirname(__DIR__) . '/src/autoload.php';

session_name('crm_session');
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
        echo $app->render('errors/419', ['title' => 'Сессия истекла']);
        exit;
    }

    $response = $app->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
    if ($response === null) {
        http_response_code(404);
        $response = $app->render('errors/404', ['title' => 'Страница не найдена']);
    }

    echo $response;
} catch (Throwable $exception) {
    http_response_code(500);
    $message = getenv('CRM_DEBUG') === 'true' ? $exception->getMessage() : 'Проверьте логи сервера.';
    error_log($exception);

    if (isset($app)) {
        echo $app->render('errors/500', ['title' => 'Ошибка', 'message' => $message]);
    } else {
        echo 'CRM не удалось запустить: ' . e($message);
    }
}
