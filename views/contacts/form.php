<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <?php if ($contact !== null): ?><input type="hidden" name="id" value="<?= e($contact['id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Name <span class="required">*</span><input name="name" required maxlength="150" value="<?= e($contact['name'] ?? '') ?>" placeholder="Anna Smith"></label>
            <label>Client<select name="client_id"><option value="0">No client</option><?php foreach ($clients as $client): ?><option value="<?= e($client['id']) ?>" <?= $selectedClient === (int) $client['id'] ? 'selected' : '' ?>><?= e($client['name']) ?><?= $client['company'] ? ' · ' . e($client['company']) : '' ?></option><?php endforeach; ?></select></label>
            <label>Job title<input name="job_title" maxlength="150" value="<?= e($contact['job_title'] ?? '') ?>" placeholder="Product manager"></label>
            <label>Email<input type="email" name="email" maxlength="150" value="<?= e($contact['email'] ?? '') ?>" placeholder="anna@example.com"></label>
            <label>Phone<input type="tel" name="phone" maxlength="50" value="<?= e($contact['phone'] ?? '') ?>" placeholder="+1 555 123-4567"></label>
        </div>
        <label>Notes<textarea name="notes" rows="5" placeholder="Role, preferences, important details"><?= e($contact['notes'] ?? '') ?></textarea></label>
        <div class="form-actions"><a class="button button-secondary" href="/contacts">Cancel</a><button class="button" type="submit"><?= $contact === null ? 'Add contact' : 'Save changes' ?></button></div>
    </form>
    <?php if ($contact !== null): ?><form class="delete-form" method="post" action="/contacts/<?= e($contact['id']) ?>/delete" onsubmit="return confirm('Delete this contact? Case links will be removed.')"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete contact</button></form><?php endif; ?>
</section>
