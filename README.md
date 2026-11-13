# CRM PHP

A small, universal case-management CRM built on plain PHP and SQLite. The case is the central CRM node: leads, issues and tasks are case types, while users, clients and contacts are entities connected to that node.

## Core model

- [x] Universal cases: issue, lead or task types, statuses, priorities, assignees, due dates and optional value;
- [x] Users: administrators, regular users and client users, with active/inactive state;
- [x] Clients and contacts: clients can be linked to a case, and several contacts can be linked to each case.

A user with the `client` role is an application identity. A client or contact is a CRM record and does not need a user account.

## Features

- cases: a central detail page, lead-by-default type, status, priority, optional client and assignee, multiple contacts, due date, value and description;
- clients: company details, notes, search, related cases and contacts;
- contacts: client membership, job title, communication details and case links;
- users: roles, unique email addresses, activity state and assigned-case counts;
- dashboard: open and unassigned cases, case flow and recent updates;
- plugins: manifest, activation and deactivation, events, actions, filters, priorities, routes and custom views;
- CSRF protection for forms, prepared SQL statements and escaped output.

Existing deals are migrated to lead cases automatically. Won deals become resolved, lost deals become closed, and other active stages become new or in-progress cases. Old `/deals` page links redirect to `/cases`.

## Requirements

PHP 8.1+ with the `pdo_sqlite` and `sqlite3` extensions. Composer and Node.js are not required.

## Running locally with lerd

The project is linked as the lerd site `crm-php`, uses PHP 8.3 and is served from `public/`.

Open [http://php-crm.localhost](http://php-crm.localhost). The database `storage/crm.sqlite` and its tables are created automatically on the first request.

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

> The base version has no authentication or authorization. User roles currently describe records and case ownership; they do not restrict access. Run it locally, or add authentication before exposing it to a network.

## Structure

```text
public/               Front controller and CSS
routes/web.php        Core routes
src/Core/             HTTP, SQLite, views, hooks and the plugin manager
src/Controllers/      Case, client, contact, user and plugin controllers
src/Repositories/     Entity data access
views/                Interface templates
plugins/              Installable plugins
database/schema.sql   SQLite schema
storage/              Local database
```

## Activity Log plugin

The `deal-activity` plugin is enabled by default. Its directory and storage table names are kept for compatibility. It:

- creates its own table in `activate()`;
- listens to client, contact, user and case events;
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
        $app->hooks->listen('case.created', function (array $case): void {
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
$app->hooks->listen('case.created', $listener, 10);
$app->hooks->dispatch('case.created', $case);

$app->hooks->addAction('custom.action', $callback, 10);
$app->hooks->doAction('custom.action', $argument);
```

A lower priority runs earlier; listeners with the same priority keep their registration order.

Core events:

- `client.created(client)`, `client.updated(client, oldClient)`, `client.deleted(client)`;
- `contact.created(contact)`, `contact.updated(contact, oldContact)`, `contact.deleted(contact)`;
- `user.created(user)`, `user.updated(user, oldUser)`, `user.deleted(user)`;
- `case.created(case)`, `case.updated(case, oldCase)`, `case.deleted(case)`;
- `case.status_changed(case, oldStatus, newStatus)`.

### Filters

A filter receives the current value and returns the changed one:

```php
$app->hooks->addFilter('dashboard.widgets', function (array $widgets): array {
    $widgets[] = ['title' => 'My widget', 'url' => null, 'items' => []];
    return $widgets;
});
```

`admin.navigation` and `dashboard.widgets` are available. Add a route with `$app->router->get()` or `post()`, and render a plugin template with `$app->render(__DIR__ . '/views/page.php', $data)`.
