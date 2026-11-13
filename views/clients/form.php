<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <?php if ($client !== null): ?><input type="hidden" name="id" value="<?= e($client['id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Name <span class="required">*</span><input name="name" required maxlength="150" value="<?= e($client['name'] ?? '') ?>" placeholder="Anna Smith"></label>
            <label>Company<input name="company" maxlength="150" value="<?= e($client['company'] ?? '') ?>" placeholder="Acme Inc."></label>
            <label>Email<input type="email" name="email" maxlength="150" value="<?= e($client['email'] ?? '') ?>" placeholder="anna@example.com"></label>
            <label>Phone<input type="tel" name="phone" maxlength="50" value="<?= e($client['phone'] ?? '') ?>" placeholder="+1 555 123-4567"></label>
        </div>
        <label>Notes<textarea name="notes" rows="5" placeholder="Context, agreements, important details"><?= e($client['notes'] ?? '') ?></textarea></label>
        <div class="form-actions"><a class="button button-secondary" href="/clients">Cancel</a><button class="button" type="submit"><?= $client === null ? 'Add client' : 'Save changes' ?></button></div>
    </form>
</section>
