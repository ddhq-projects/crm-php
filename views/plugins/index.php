<div class="page-actions"><p class="muted">Плагины подключают события, фильтры, страницы и пункты меню без изменений ядра.</p></div>
<div class="plugin-grid">
    <?php foreach ($plugins as $plugin): ?>
        <article class="panel plugin-card">
            <div class="panel-heading"><span class="badge <?= $plugin['enabled'] ? 'badge-success' : '' ?>"><?= $plugin['enabled'] ? 'Включён' : 'Выключен' ?></span><span class="muted">v<?= e($plugin['version']) ?></span></div>
            <h2><?= e($plugin['name']) ?></h2>
            <p class="muted"><?= e($plugin['description']) ?></p>
            <div class="plugin-meta"><span>Автор: <?= e($plugin['author']) ?></span><code><?= e($plugin['slug']) ?></code></div>
            <form method="post" action="/plugins/toggle">
                <?= csrf_field() ?><input type="hidden" name="slug" value="<?= e($plugin['slug']) ?>"><input type="hidden" name="action" value="<?= $plugin['enabled'] ? 'disable' : 'enable' ?>">
                <button class="button <?= $plugin['enabled'] ? 'button-secondary' : '' ?>" type="submit"><?= $plugin['enabled'] ? 'Выключить' : 'Включить' ?></button>
            </form>
        </article>
    <?php endforeach; ?>
</div>
<section class="panel developer-note"><h2>Свой плагин</h2><p>Скопируйте <code>plugins/deal-activity</code>, измените манифест и зарегистрируйте обработчики через <code>$app-&gt;hooks</code>. Пример и доступные события описаны в README.</p></section>
