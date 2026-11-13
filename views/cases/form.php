<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <?php if ($case !== null): ?><input type="hidden" name="id" value="<?= e($case['id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Case title <span class="required">*</span><input name="title" required maxlength="180" value="<?= e($case['title'] ?? '') ?>" placeholder="Customer request"></label>
            <label>Type<select name="kind"><?php foreach ($types as $key => $label): ?><option value="<?= e($key) ?>" <?= ($case['kind'] ?? 'lead') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <label>Status<select name="status"><?php foreach ($statuses as $key => $label): ?><option value="<?= e($key) ?>" <?= ($case['status'] ?? 'new') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <label>Priority<select name="priority"><?php foreach ($priorities as $key => $label): ?><option value="<?= e($key) ?>" <?= ($case['priority'] ?? 'normal') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <label>Client<select name="client_id"><option value="0">No client</option><?php foreach ($clients as $client): ?><option value="<?= e($client['id']) ?>" <?= $selectedClient === (int) $client['id'] ? 'selected' : '' ?>><?= e($client['name']) ?><?= $client['company'] ? ' · ' . e($client['company']) : '' ?></option><?php endforeach; ?></select></label>
            <label>Assignee<select name="assignee_id"><option value="0">Unassigned</option><?php foreach ($users as $user): ?><option value="<?= e($user['id']) ?>" <?= (int) ($case['assignee_id'] ?? 0) === (int) $user['id'] ? 'selected' : '' ?>><?= e($user['name']) ?> · <?= e($user['role']) ?></option><?php endforeach; ?></select></label>
            <label>Due date<input type="date" name="due_date" value="<?= e($case['due_date'] ?? '') ?>"></label>
            <label>Value, $<input type="number" name="amount" min="0" step="0.01" value="<?= e($case['amount'] ?? '0') ?>"></label>
        </div>
        <label>Linked contacts<select name="contact_ids[]" multiple size="6"><?php foreach ($contacts as $contact): ?><option value="<?= e($contact['id']) ?>" <?= in_array((int) $contact['id'], $selectedContacts, true) ? 'selected' : '' ?>><?= e($contact['name']) ?><?= $contact['client_name'] ? ' · ' . e($contact['client_name']) : '' ?></option><?php endforeach; ?></select><small class="field-help">Optional. Use Cmd/Ctrl to select several contacts.</small></label>
        <label>Description<textarea name="description" rows="6" placeholder="Context, requirements, next step"><?= e($case['description'] ?? '') ?></textarea></label>
        <div class="form-actions"><a class="button button-secondary" href="<?= $case === null ? '/cases' : '/cases/' . e($case['id']) ?>">Cancel</a><button class="button" type="submit"><?= $case === null ? 'Create case' : 'Save changes' ?></button></div>
    </form>
    <?php if ($case !== null): ?><form class="delete-form" method="post" action="/cases/<?= e($case['id']) ?>/delete" onsubmit="return confirm('Delete this case?')"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete case</button></form><?php endif; ?>
</section>
