<section class="panel form-panel">
    <?php if ($clients === []): ?>
        <div class="empty"><p>Add a client first — every deal belongs to a client.</p><a class="button" href="/clients/create">Add client</a></div>
    <?php else: ?>
        <form method="post" action="<?= e($action) ?>">
            <?= csrf_field() ?>
            <div class="form-grid">
                <label>Deal title <span class="required">*</span><input name="title" required maxlength="180" value="<?= e($deal['title'] ?? '') ?>" placeholder="CRM rollout"></label>
                <label>Client <span class="required">*</span><select name="client_id" required><?php foreach ($clients as $client): ?><option value="<?= e($client['id']) ?>" <?= $selectedClient === (int) $client['id'] ? 'selected' : '' ?>><?= e($client['name']) ?><?= $client['company'] ? ' · ' . e($client['company']) : '' ?></option><?php endforeach; ?></select></label>
                <label>Amount, $<input type="number" name="amount" min="0" step="0.01" value="<?= e($deal['amount'] ?? '0') ?>"></label>
                <label>Stage<select name="stage"><?php foreach ($stages as $key => $label): ?><option value="<?= e($key) ?>" <?= ($deal['stage'] ?? 'new') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                <label>Expected close date<input type="date" name="expected_close_date" value="<?= e($deal['expected_close_date'] ?? '') ?>"></label>
            </div>
            <label>Notes<textarea name="notes" rows="5" placeholder="Scope, next step, agreements"><?= e($deal['notes'] ?? '') ?></textarea></label>
            <div class="form-actions"><a class="button button-secondary" href="/deals">Cancel</a><button class="button" type="submit"><?= $deal === null ? 'Create deal' : 'Save changes' ?></button></div>
        </form>
        <?php if ($deal !== null): ?><form class="delete-form" method="post" action="/deals/<?= e($deal['id']) ?>/delete" onsubmit="return confirm('Delete this deal?')"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete deal</button></form><?php endif; ?>
    <?php endif; ?>
</section>
