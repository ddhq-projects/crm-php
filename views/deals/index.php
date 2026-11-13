<div class="page-actions">
    <div class="filter-links"><a <?= $activeStage === '' ? 'class="active"' : '' ?> href="/deals">All</a><?php foreach ($stages as $key => $label): ?><a <?= $activeStage === $key ? 'class="active"' : '' ?> href="/deals?stage=<?= e($key) ?>"><?= e($label) ?></a><?php endforeach; ?></div>
    <a class="button" href="/deals/create">New deal</a>
</div>

<section class="panel">
    <?php if ($deals === []): ?>
        <div class="empty"><p>No deals in this view.</p><a class="button" href="/deals/create">Create deal</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Deal</th><th>Client</th><th>Amount</th><th>Close date</th><th>Stage</th><th></th></tr></thead>
            <tbody><?php foreach ($deals as $deal): ?><tr>
                <td><a class="table-title" href="/deals/<?= e($deal['id']) ?>/edit"><?= e($deal['title']) ?></a></td>
                <td><a href="/clients/<?= e($deal['client_id']) ?>"><?= e($deal['client_name']) ?></a><small><?= e($deal['client_company']) ?></small></td>
                <td class="nowrap"><?= money($deal['amount']) ?></td>
                <td><?= $deal['expected_close_date'] ? e(date('M j, Y', strtotime($deal['expected_close_date']))) : '—' ?></td>
                <td><form class="stage-form" method="post" action="/deals/<?= e($deal['id']) ?>/stage"><?= csrf_field() ?><select class="stage-select stage-<?= e($deal['stage']) ?>" name="stage" onchange="this.form.submit()" aria-label="Deal stage"><?php foreach ($stages as $key => $label): ?><option value="<?= e($key) ?>" <?= $deal['stage'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><noscript><button class="button button-small" type="submit">Save</button></noscript></form></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/deals/<?= e($deal['id']) ?>/edit">Edit</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
