<div class="page-actions">
    <form class="search-form" method="get" action="/clients">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Name, company or email" aria-label="Search clients">
        <button class="button button-secondary" type="submit">Search</button>
    </form>
    <a class="button" href="/clients/create">Add client</a>
</div>

<section class="panel">
    <?php if ($clients === []): ?>
        <div class="empty"><p><?= $search === '' ? 'No clients yet.' : 'Nothing matched this search.' ?></p><?php if ($search === ''): ?><a class="button" href="/clients/create">Add the first client</a><?php endif; ?></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Client</th><th>Communication</th><th>Cases</th><th>Open value</th><th></th></tr></thead>
            <tbody><?php foreach ($clients as $client): ?><tr>
                <td><a class="table-title" href="/clients/<?= e($client['id']) ?>"><?= e($client['name']) ?></a><small><?= e($client['company'] ?: 'No company') ?></small></td>
                <td><?= e($client['email'] ?: '—') ?><small><?= e($client['phone']) ?></small></td>
                <td><?= e($client['cases_count']) ?></td>
                <td><?= money($client['open_amount']) ?></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/clients/<?= e($client['id']) ?>/edit">Edit</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
