<div class="page-actions">
    <a class="back-link" href="/clients">← All clients</a>
    <div class="actions"><a class="button button-secondary" href="/clients/<?= e($client['id']) ?>/edit">Edit</a><a class="button button-secondary" href="/contacts/create?client_id=<?= e($client['id']) ?>">Add contact</a><a class="button" href="/cases/create?client_id=<?= e($client['id']) ?>">Add case</a></div>
</div>

<div class="detail-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Client details</h2><span class="muted">since <?= e(date('M j, Y', strtotime($client['created_at']))) ?></span></div>
        <dl class="details">
            <div><dt>Company</dt><dd><?= e($client['company'] ?: '—') ?></dd></div>
            <div><dt>Email</dt><dd><?php if ($client['email']): ?><a href="mailto:<?= e($client['email']) ?>"><?= e($client['email']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Phone</dt><dd><?php if ($client['phone']): ?><a href="tel:<?= e($client['phone']) ?>"><?= e($client['phone']) ?></a><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Notes</dt><dd class="pre-line"><?= e($client['notes'] ?: '—') ?></dd></div>
        </dl>
        <form method="post" action="/clients/<?= e($client['id']) ?>/delete" onsubmit="return confirm('Delete this client? Cases and contacts will be kept.')">
            <?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete client</button>
        </form>
    </section>
    <section class="panel">
        <div class="panel-heading"><h2>Client cases</h2><span class="muted"><?= count($cases) ?></span></div>
        <?php if ($cases === []): ?><p class="muted">This client has no cases yet.</p><?php else: ?>
            <ul class="deal-list"><?php foreach ($cases as $case): ?><li><div><a class="table-title" href="/cases/<?= e($case['id']) ?>">#<?= e($case['id']) ?> <?= e($case['title']) ?></a><small><?= e($statuses[$case['status']]) ?> · <?= e($case['assignee_name'] ?: 'Unassigned') ?></small></div><a href="/cases/<?= e($case['id']) ?>/edit">→</a></li><?php endforeach; ?></ul>
        <?php endif; ?>
    </section>
    <section class="panel">
        <div class="panel-heading"><h2>Contacts</h2><span class="muted"><?= count($contacts) ?></span></div>
        <?php if ($contacts === []): ?><p class="muted">This client has no contacts yet.</p><?php else: ?>
            <ul class="deal-list"><?php foreach ($contacts as $contact): ?><li><div><a class="table-title" href="/contacts/<?= e($contact['id']) ?>/edit"><?= e($contact['name']) ?></a><small><?= e($contact['job_title'] ?: $contact['email']) ?></small></div><a href="/contacts/<?= e($contact['id']) ?>/edit">→</a></li><?php endforeach; ?></ul>
        <?php endif; ?>
    </section>
</div>
