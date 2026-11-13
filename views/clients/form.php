<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <?php if ($client !== null): ?><input type="hidden" name="id" value="<?= e($client['id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Имя <span class="required">*</span><input name="name" required maxlength="150" value="<?= e($client['name'] ?? '') ?>" placeholder="Анна Смирнова"></label>
            <label>Компания<input name="company" maxlength="150" value="<?= e($client['company'] ?? '') ?>" placeholder="ООО Пример"></label>
            <label>Email<input type="email" name="email" maxlength="150" value="<?= e($client['email'] ?? '') ?>" placeholder="anna@example.ru"></label>
            <label>Телефон<input type="tel" name="phone" maxlength="50" value="<?= e($client['phone'] ?? '') ?>" placeholder="+7 999 123-45-67"></label>
        </div>
        <label>Заметки<textarea name="notes" rows="5" placeholder="Контекст, договорённости, важные детали"><?= e($client['notes'] ?? '') ?></textarea></label>
        <div class="form-actions"><a class="button button-secondary" href="/clients">Отмена</a><button class="button" type="submit"><?= $client === null ? 'Добавить клиента' : 'Сохранить изменения' ?></button></div>
    </form>
</section>
