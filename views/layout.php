<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'CRM') ?> · LiteCRM</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="/">
            <span class="brand-mark">L</span>
            <span>LiteCRM<small>клиенты и продажи</small></span>
        </a>
        <nav class="navigation" aria-label="Основная навигация">
            <?php foreach ($navigation as $item): ?>
                <?php $active = $item['url'] === '/' ? $currentPath === '/' : str_starts_with($currentPath, $item['url']); ?>
                <a href="<?= e($item['url']) ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-note">PHP + SQLite<br>расширяется плагинами</div>
    </aside>
    <main class="main">
        <header class="topbar">
            <h1><?= e($title ?? 'CRM') ?></h1>
        </header>
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</div>
</body>
</html>
