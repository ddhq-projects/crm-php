<div class="page-actions">
    <form class="search-form" method="get" action="/clients">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Имя, компания или email" aria-label="Поиск клиентов">
        <button class="button button-secondary" type="submit">Найти</button>
    </form>
    <a class="button" href="/clients/create">Добавить клиента</a>
</div>

<section class="panel">
    <?php if ($clients === []): ?>
        <div class="empty"><p><?= $search === '' ? 'Клиентов пока нет.' : 'По запросу ничего не найдено.' ?></p><?php if ($search === ''): ?><a class="button" href="/clients/create">Добавить первого клиента</a><?php endif; ?></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Клиент</th><th>Контакты</th><th>Сделки</th><th>В работе</th><th></th></tr></thead>
            <tbody><?php foreach ($clients as $client): ?><tr>
                <td><a class="table-title" href="/clients/<?= e($client['id']) ?>"><?= e($client['name']) ?></a><small><?= e($client['company'] ?: 'Без компании') ?></small></td>
                <td><?= e($client['email'] ?: '—') ?><small><?= e($client['phone']) ?></small></td>
                <td><?= e($client['deals_count']) ?></td>
                <td><?= money($client['open_amount']) ?></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/clients/<?= e($client['id']) ?>/edit">Изменить</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
