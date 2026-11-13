<div class="page-actions">
    <a class="back-link" href="/clients">← Все клиенты</a>
    <div class="actions"><a class="button button-secondary" href="/clients/<?= e($client['id']) ?>/edit">Изменить</a><a class="button" href="/deals/create?client_id=<?= e($client['id']) ?>">Добавить сделку</a></div>
</div>

<div class="detail-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Контактные данные</h2><span class="muted">с <?= e(date('d.m.Y', strtotime($client['created_at']))) ?></span></div>
        <dl class="details">
            <div><dt>Компания</dt><dd><?= e($client['company'] ?: '—') ?></dd></div>
            <div><dt>Email</dt><dd><?php if ($client['email']): ?><a href="mailto:<?= e($client['email']) ?>"><?= e($client['email']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Телефон</dt><dd><?php if ($client['phone']): ?><a href="tel:<?= e($client['phone']) ?>"><?= e($client['phone']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Заметки</dt><dd class="pre-line"><?= e($client['notes'] ?: '—') ?></dd></div>
        </dl>
        <form method="post" action="/clients/<?= e($client['id']) ?>/delete" onsubmit="return confirm('Удалить клиента и все его сделки?')">
            <?= csrf_field() ?><button class="button button-danger button-small" type="submit">Удалить клиента</button>
        </form>
    </section>
    <section class="panel">
        <div class="panel-heading"><h2>Сделки клиента</h2><span class="muted"><?= count($deals) ?></span></div>
        <?php if ($deals === []): ?><p class="muted">У клиента ещё нет сделок.</p><?php else: ?>
            <ul class="deal-list"><?php foreach ($deals as $deal): ?><li><div><a class="table-title" href="/deals/<?= e($deal['id']) ?>/edit"><?= e($deal['title']) ?></a><small><?= e($stages[$deal['stage']]) ?> · <?= money($deal['amount']) ?></small></div><a href="/deals/<?= e($deal['id']) ?>/edit">→</a></li><?php endforeach; ?></ul>
        <?php endif; ?>
    </section>
</div>
