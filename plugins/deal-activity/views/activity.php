<div class="page-actions"><p class="muted">До 200 последних событий, записанных плагином «Журнал активности».</p><a class="button button-secondary" href="/plugins">Настроить плагин</a></div>
<section class="panel">
    <?php if ($activities === []): ?>
        <div class="empty"><p>Событий пока нет. Добавьте клиента или создайте сделку.</p><a class="button" href="/clients/create">Добавить клиента</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Событие</th><th>Описание</th><th>Объект</th><th>Дата</th></tr></thead>
            <tbody><?php foreach ($activities as $activity): ?><tr>
                <td><code><?= e($activity['event']) ?></code></td>
                <td class="white-space-normal"><?= e($activity['message']) ?></td>
                <td><?= e($activity['entity_type']) ?> #<?= e($activity['entity_id']) ?></td>
                <td><?= e(date('d.m.Y H:i:s', strtotime($activity['created_at']))) ?></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
