<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <?php if ($user !== null): ?><input type="hidden" name="id" value="<?= e($user['id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Name <span class="required">*</span><input name="name" required maxlength="150" value="<?= e($user['name'] ?? '') ?>" placeholder="Alex Morgan"></label>
            <label>Email <span class="required">*</span><input type="email" name="email" required maxlength="150" value="<?= e($user['email'] ?? '') ?>" placeholder="alex@example.com"></label>
            <label>Role<select name="role"><?php foreach ($roles as $key => $label): ?><option value="<?= e($key) ?>" <?= ($user['role'] ?? 'user') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
        </div>
        <label class="checkbox-label"><input type="checkbox" name="active" value="1" <?= !isset($user['active']) || $user['active'] ? 'checked' : '' ?>> Active user</label>
        <div class="form-actions"><a class="button button-secondary" href="/users">Cancel</a><button class="button" type="submit"><?= $user === null ? 'Add user' : 'Save changes' ?></button></div>
    </form>
    <?php if ($user !== null): ?><form class="delete-form" method="post" action="/users/<?= e($user['id']) ?>/delete" onsubmit="return confirm('Delete this user? Assigned cases will become unassigned.')"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Delete user</button></form><?php endif; ?>
</section>
