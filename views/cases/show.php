<div class="page-actions">
    <a class="back-link" href="/cases">← All cases</a>
    <div class="actions"><a class="button button-secondary" href="/cases/<?= e($case['id']) ?>/edit">Edit case</a><a class="button" href="/cases/create?client_id=<?= e($case['client_id'] ?? 0) ?>">New related case</a></div>
</div>

<div class="case-summary">
    <span class="badge"><?= e($type) ?></span>
    <span class="badge status-<?= e($case['status']) ?>"><?= e($status) ?></span>
    <span class="badge priority-<?= e($case['priority']) ?>"><?= e($priority) ?> priority</span>
</div>

<div class="detail-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Case details</h2><span class="muted">updated <?= e(date('M j, Y', strtotime($case['updated_at']))) ?></span></div>
        <dl class="details">
            <div><dt>Client</dt><dd><?php if ($case['client_id']): ?><a href="/clients/<?= e($case['client_id']) ?>"><?= e($case['client_name']) ?></a><?php if ($case['client_company']): ?><small class="detail-note"><?= e($case['client_company']) ?></small><?php endif; ?><?php else: ?>—<?php endif; ?></dd></div>
            <div><dt>Assignee</dt><dd><?php if ($case['assignee_id']): ?><a href="/users/<?= e($case['assignee_id']) ?>/edit"><?= e($case['assignee_name']) ?></a><?php else: ?>Unassigned<?php endif; ?></dd></div>
            <div><dt>Due date</dt><dd><?= $case['due_date'] ? e(date('M j, Y', strtotime($case['due_date']))) : '—' ?></dd></div>
            <div><dt>Value</dt><dd><?= $case['amount'] > 0 ? money($case['amount']) : '—' ?></dd></div>
            <div><dt>Created</dt><dd><?= e(date('M j, Y g:i A', strtotime($case['created_at']))) ?></dd></div>
        </dl>
        <h2>Description</h2>
        <p class="pre-line case-description"><?= e($case['description'] ?: 'No description yet.') ?></p>
    </section>
    <section class="panel">
        <div class="panel-heading"><h2>Linked contacts</h2><span class="muted"><?= count($contacts) ?></span></div>
        <?php if ($contacts === []): ?>
            <div class="empty"><p>No contacts linked to this case.</p><a class="button button-secondary" href="/cases/<?= e($case['id']) ?>/edit">Link contacts</a></div>
        <?php else: ?>
            <ul class="deal-list"><?php foreach ($contacts as $contact): ?><li><div><a class="table-title" href="/contacts/<?= e($contact['id']) ?>/edit"><?= e($contact['name']) ?></a><small><?= e($contact['job_title'] ?: 'Contact') ?><?= $contact['client_name'] ? ' · ' . e($contact['client_name']) : '' ?></small></div><?php if ($contact['email']): ?><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a><?php else: ?><a href="/contacts/<?= e($contact['id']) ?>/edit">→</a><?php endif; ?></li><?php endforeach; ?></ul>
        <?php endif; ?>
    </section>
</div>
