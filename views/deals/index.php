<div class="page-actions">
    <div class="filter-links"><a <?= $activeStage === '' ? 'class="active"' : '' ?> href="/deals">Все</a><?php foreach ($stages as $key => $label): ?><a <?= $activeStage === $key ? 'class="active"' : '' ?> href="/deals?stage=<?= e($key) ?>"><?= e($label) ?></a><?php endforeach; ?></div>
    <a class="button" href="/deals/create">Новая сделка</a>
</div>

<section class="panel">
    <?php if ($deals === []): ?>
        <div class="empty"><p>В этом представлении сделок нет.</p><a class="button" href="/deals/create">Создать сделку</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Сделка</th><th>Клиент</th><th>Сумма</th><th>Закрытие</th><th>Этап</th><th></th></tr></thead>
            <tbody><?php foreach ($deals as $deal): ?><tr>
                <td><a class="table-title" href="/deals/<?= e($deal['id']) ?>/edit"><?= e($deal['title']) ?></a></td>
                <td><a href="/clients/<?= e($deal['client_id']) ?>"><?= e($deal['client_name']) ?></a><small><?= e($deal['client_company']) ?></small></td>
                <td class="nowrap"><?= money($deal['amount']) ?></td>
                <td><?= $deal['expected_close_date'] ? e(date('d.m.Y', strtotime($deal['expected_close_date']))) : '—' ?></td>
                <td><form class="stage-form" method="post" action="/deals/<?= e($deal['id']) ?>/stage"><?= csrf_field() ?><select class="stage-select stage-<?= e($deal['stage']) ?>" name="stage" onchange="this.form.submit()" aria-label="Этап сделки"><?php foreach ($stages as $key => $label): ?><option value="<?= e($key) ?>" <?= $deal['stage'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><noscript><button class="button button-small" type="submit">Сохранить</button></noscript></form></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/deals/<?= e($deal['id']) ?>/edit">Изменить</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
