<div class="page-actions">
    <a class="back-link" href="/clients">← All clients</a>
    <div class="actions"><a class="button button-secondary" href="/clients/<?= e($client['id']) ?>/edit">Edit</a><a class="button" href="/deals/create?client_id=<?= e($client['id']) ?>">Add deal</a></div>
</div>

<div class="detail-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Contact details</h2><span class="muted">since <?= e(date('M j, Y', strtotime($client['created_at']))) ?></span></div>
        <dl class="details">
            <div><dt>Company</dt><dd><?= e($client['company'] ?: '—') ?></dd></div>
            <div><dt>Email</dt><dd><?php if ($client['email']): ?><a href="mailto:<?= e($client['email']) ?>"><?= e($client['email']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Phone</dt><dd><?php if ($client['phone']): ?><a href="tel:<?= e($client['phone']) ?>"><?= e($client['phone']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Notes</dt><dd class="pre-line"><?= e($client['notes'] ?: '—') ?></dd></div>
        </dl>
        <form method="post" action="/clients/<?= e($client['id']) ?>/delete" onsubmit="return confirm('Delete this client and all of its deals?')">
            <?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete client</button>
        </form>
    </section>
    <section class="panel">
        <div class="panel-heading"><h2>Client deals</h2><span class="muted"><?= count($deals) ?></span></div>
        <?php if ($deals === []): ?><p class="muted">This client has no deals yet.</p><?php else: ?>
            <ul class="deal-list"><?php foreach ($deals as $deal): ?><li><div><a class="table-title" href="/deals/<?= e($deal['id']) ?>/edit"><?= e($deal['title']) ?></a><small><?= e($stages[$deal['stage']]) ?> · <?= money($deal['amount']) ?></small></div><a href="/deals/<?= e($deal['id']) ?>/edit">→</a></li><?php endforeach; ?></ul>
        <?php endif; ?>
    </section>
</div>
