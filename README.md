# CRM PHP

A small CRM built on plain PHP and SQLite: clients, a deal pipeline and extensions through plugins. Closer in spirit to Bitrix24, amoCRM and Salesforce, but without a heavy framework or external dependencies.

## Features

- clients: contacts, company, notes, search, create, edit and delete;
- deals: amount, client, expected close date, notes and six pipeline stages;
- dashboard: active and won amounts, the pipeline and recent deals;
- plugins: manifest, activation and deactivation, events, actions, filters, priorities, routes and custom views;
- CSRF protection for forms, prepared SQL statements and escaped output.

## Requirements

PHP 8.1+ with the `pdo_sqlite` and `sqlite3` extensions. Composer and Node.js are not required.

## Running locally with lerd

The app is a plain PHP project served from `public/`, which lerd detects as the document root on link.

```bash
# from the project root
lerd link crm-php
```

Open [http://crm-php.localhost](http://crm-php.localhost). The database `storage/crm.sqlite` and its tables are created automatically on the first request.

## Running without lerd

Any PHP-capable web server works as long as its document root is `public/`. With the PHP built-in server:

```bash
php -S 127.0.0.1:8080 -t public
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080).

To use another database file:

```bash
CRM_DATABASE=/full/path/crm.sqlite php -S 127.0.0.1:8080 -t public
```

Add `CRM_DEBUG=true` to show error messages during development.

> The base version has no authentication. Run it locally, or add authentication before exposing it to a network.

## Structure

```text
public/               Front controller and CSS
routes/web.php        Core routes
src/Core/             HTTP, SQLite, views, hooks and the plugin manager
src/Controllers/      CRM controllers
src/Repositories/     Client and deal data access
views/                Interface templates
plugins/              Installable plugins
database/schema.sql   SQLite schema
storage/              Local database
```

## Activity Log plugin

The `deal-activity` plugin is enabled by default. It:

- creates its own table in `activate()`;
- listens to `client.created`, `deal.created`, `deal.stage_changed` and `deal.deleted`;
- adds a widget through the `dashboard.widgets` filter;
- adds a menu item through the `admin.navigation` filter;
- registers an `/activity` page with its own template;
- keeps its data when the plugin is disabled.

Manage it in the **Plugins** section. Disabling it removes the handlers, the route and the widget from the next request on.

## Extension API

A plugin lives in its own directory, `plugins/my-plugin`, and contains `plugin.php`:

```php
return [
    'slug' => 'my-plugin',
    'name' => 'My plugin',
    'version' => '1.0.0',
    'description' => 'What the extension does.',
    'author' => 'Your name',
    'class' => MyPlugin\Plugin::class,
    'class_file' => 'Plugin.php',
    'default_enabled' => false,
];
```

The class implements `Crm\Core\PluginInterface`:

```php
final class Plugin implements PluginInterface
{
    public function register(App $app): void
    {
        $app->hooks->listen('deal.created', function (array $deal): void {
            // React to the event.
        }, priority: 20);

        $app->hooks->addFilter('admin.navigation', function (array $items): array {
            $items[] = ['url' => '/my-plugin', 'label' => 'My plugin'];
            return $items;
        });
    }

    public function activate(App $app): void {}
    public function deactivate(App $app): void {}
}
```

`register()` runs on every request for enabled plugins only. `activate()` runs when the plugin is enabled, `deactivate()` when it is disabled. Plugins are trusted PHP code and run with the application's privileges.

### Events and actions

```php
$app->hooks->listen('deal.created', $listener, 10);
$app->hooks->dispatch('deal.created', $deal);

$app->hooks->addAction('custom.action', $callback, 10);
$app->hooks->doAction('custom.action', $argument);
```

A lower priority runs earlier; listeners with the same priority keep their registration order.

Core events:

- `client.created(client)`, `client.updated(client, oldClient)`, `client.deleted(client)`;
- `deal.created(deal)`, `deal.updated(deal, oldDeal)`, `deal.deleted(deal)`;
- `deal.stage_changed(deal, oldStage, newStage)`.

### Filters

A filter receives the current value and returns the changed one:

```php
$app->hooks->addFilter('dashboard.widgets', function (array $widgets): array {
    $widgets[] = ['title' => 'My widget', 'url' => null, 'items' => []];
    return $widgets;
});
```

`admin.navigation` and `dashboard.widgets` are available. Add a route with `$app->router->get()` or `post()`, and render a plugin template with `$app->render(__DIR__ . '/views/page.php', $data)`.
