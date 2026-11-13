<div class="page-actions"><p class="muted">Plugins add events, filters, pages and menu items without touching the core.</p></div>
<div class="plugin-grid">
    <?php foreach ($plugins as $plugin): ?>
        <article class="panel plugin-card">
            <div class="panel-heading"><span class="badge <?= $plugin['enabled'] ? 'badge-success' : '' ?>"><?= $plugin['enabled'] ? 'Enabled' : 'Disabled' ?></span><span class="muted">v<?= e($plugin['version']) ?></span></div>
            <h2><?= e($plugin['name']) ?></h2>
            <p class="muted"><?= e($plugin['description']) ?></p>
            <div class="plugin-meta"><span>Author: <?= e($plugin['author']) ?></span><code><?= e($plugin['slug']) ?></code></div>
            <form method="post" action="/plugins/toggle">
                <?= csrf_field() ?><input type="hidden" name="slug" value="<?= e($plugin['slug']) ?>"><input type="hidden" name="action" value="<?= $plugin['enabled'] ? 'disable' : 'enable' ?>">
                <button class="button <?= $plugin['enabled'] ? 'button-secondary' : '' ?>" type="submit"><?= $plugin['enabled'] ? 'Disable' : 'Enable' ?></button>
            </form>
        </article>
    <?php endforeach; ?>
</div>
<section class="panel developer-note"><h2>Your own plugin</h2><p>Copy <code>plugins/deal-activity</code>, edit the manifest and register handlers through <code>$app-&gt;hooks</code>. The README describes the example and the available events.</p></section>
