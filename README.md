# CRM PHP

Небольшая CRM на PHP и SQLite: учёт клиентов, воронка сделок и расширения через плагины. По идее ближе к Bitrix24, amoCRM и Salesforce, но без тяжёлого фреймворка и внешних зависимостей.

## Возможности

- клиенты: контакты, компания, заметки, поиск, создание, редактирование и удаление;
- сделки: сумма, клиент, планируемая дата закрытия, заметки и шесть этапов воронки;
- панель показателей: активные и выигранные суммы, воронка и последние сделки;
- плагины: манифест, активация и деактивация, события, действия, фильтры, приоритеты, маршруты и собственные шаблоны;
- CSRF-защита форм, подготовленные SQL-запросы и экранирование вывода.

## Запуск

Требуется PHP 8.1+ с расширениями `pdo_sqlite` и `sqlite3`. Composer и Node.js не нужны.

```bash
php -S 127.0.0.1:8080 -t public
```

Откройте [http://127.0.0.1:8080](http://127.0.0.1:8080). База `storage/crm.sqlite` и таблицы создаются автоматически при первом запуске.

Для другого файла базы:

```bash
CRM_DATABASE=/полный/путь/crm.sqlite php -S 127.0.0.1:8080 -t public
```

Для отображения текста ошибок в разработке добавьте `CRM_DEBUG=true`.

> В базовой версии нет авторизации. Запускайте её локально или добавьте аутентификацию перед публикацией в сети.

## Структура

```text
public/               Точка входа и CSS
routes/web.php        Маршруты ядра
src/Core/             HTTP, SQLite, шаблоны, хуки и менеджер плагинов
src/Controllers/      Контроллеры CRM
src/Repositories/     Работа с клиентами и сделками
views/                Шаблоны интерфейса
plugins/              Устанавливаемые плагины
database/schema.sql   Схема SQLite
storage/              Локальная база данных
```

## Плагин «Журнал активности»

Плагин `deal-activity` включён по умолчанию. Он:

- создаёт собственную таблицу в `activate()`;
- слушает `client.created`, `deal.created`, `deal.stage_changed` и `deal.deleted`;
- добавляет виджет через фильтр `dashboard.widgets`;
- добавляет пункт меню через фильтр `admin.navigation`;
- регистрирует страницу `/activity` с собственным шаблоном;
- не удаляет данные при выключении.

Управление находится в разделе **Плагины**. Отключение убирает обработчики, маршрут и виджет со следующего запроса.

## API расширений

Плагин находится в отдельном каталоге `plugins/my-plugin` и содержит `plugin.php`:

```php
return [
    'slug' => 'my-plugin',
    'name' => 'Мой плагин',
    'version' => '1.0.0',
    'description' => 'Описание расширения.',
    'author' => 'Ваше имя',
    'class' => MyPlugin\Plugin::class,
    'class_file' => 'Plugin.php',
    'default_enabled' => false,
];
```

Класс реализует `Crm\Core\PluginInterface`:

```php
final class Plugin implements PluginInterface
{
    public function register(App $app): void
    {
        $app->hooks->listen('deal.created', function (array $deal): void {
            // Реакция на событие.
        }, priority: 20);

        $app->hooks->addFilter('admin.navigation', function (array $items): array {
            $items[] = ['url' => '/my-plugin', 'label' => 'Мой плагин'];
            return $items;
        });
    }

    public function activate(App $app): void {}
    public function deactivate(App $app): void {}
}
```

`register()` вызывается при каждом запросе только у включённых плагинов. `activate()` вызывается при включении, `deactivate()` — при выключении. Плагины — доверенный PHP-код и выполняются с правами приложения.

### События и действия

```php
$app->hooks->listen('deal.created', $listener, 10);
$app->hooks->dispatch('deal.created', $deal);

$app->hooks->addAction('custom.action', $callback, 10);
$app->hooks->doAction('custom.action', $argument);
```

Меньший приоритет выполняется раньше; при одинаковом приоритете сохраняется порядок регистрации.

События ядра:

- `client.created(client)`, `client.updated(client, oldClient)`, `client.deleted(client)`;
- `deal.created(deal)`, `deal.updated(deal, oldDeal)`, `deal.deleted(deal)`;
- `deal.stage_changed(deal, oldStage, newStage)`.

### Фильтры

Фильтр получает текущее значение и возвращает изменённое:

```php
$app->hooks->addFilter('dashboard.widgets', function (array $widgets): array {
    $widgets[] = ['title' => 'Мой виджет', 'url' => null, 'items' => []];
    return $widgets;
});
```

Доступны `admin.navigation` и `dashboard.widgets`. Маршрут добавляется через `$app->router->get()` или `post()`, шаблон плагина выводится через `$app->render(__DIR__ . '/views/page.php', $data)`.