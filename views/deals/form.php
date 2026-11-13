<section class="panel form-panel">
    <?php if ($clients === []): ?>
        <div class="empty"><p>Сначала добавьте клиента — сделка всегда связана с клиентом.</p><a class="button" href="/clients/create">Добавить клиента</a></div>
    <?php else: ?>
        <form method="post" action="<?= e($action) ?>">
            <?= csrf_field() ?>
            <div class="form-grid">
                <label>Название сделки <span class="required">*</span><input name="title" required maxlength="180" value="<?= e($deal['title'] ?? '') ?>" placeholder="Внедрение CRM"></label>
                <label>Клиент <span class="required">*</span><select name="client_id" required><?php foreach ($clients as $client): ?><option value="<?= e($client['id']) ?>" <?= $selectedClient === (int) $client['id'] ? 'selected' : '' ?>><?= e($client['name']) ?><?= $client['company'] ? ' · ' . e($client['company']) : '' ?></option><?php endforeach; ?></select></label>
                <label>Сумма, ₽<input type="number" name="amount" min="0" step="0.01" value="<?= e($deal['amount'] ?? '0') ?>"></label>
                <label>Этап<select name="stage"><?php foreach ($stages as $key => $label): ?><option value="<?= e($key) ?>" <?= ($deal['stage'] ?? 'new') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                <label>Планируемая дата закрытия<input type="date" name="expected_close_date" value="<?= e($deal['expected_close_date'] ?? '') ?>"></label>
            </div>
            <label>Заметки<textarea name="notes" rows="5" placeholder="Состав работ, следующий шаг, договорённости"><?= e($deal['notes'] ?? '') ?></textarea></label>
            <div class="form-actions"><a class="button button-secondary" href="/deals">Отмена</a><button class="button" type="submit"><?= $deal === null ? 'Создать сделку' : 'Сохранить изменения' ?></button></div>
        </form>
        <?php if ($deal !== null): ?><form class="delete-form" method="post" action="/deals/<?= e($deal['id']) ?>/delete" onsubmit="return confirm('Удалить сделку?')"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Удалить сделку</button></form><?php endif; ?>
    <?php endif; ?>
</section>
