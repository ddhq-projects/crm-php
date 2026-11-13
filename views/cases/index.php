<div class="page-actions">
    <form class="filter-form" method="get" action="/cases">
        <select name="kind" aria-label="Case type">
            <option value="">All types</option>
            <?php foreach ($types as $key => $label): ?><option value="<?= e($key) ?>" <?= $activeType === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select>
        <select name="status" aria-label="Case status">
            <option value="">All statuses</option>
            <?php foreach ($statuses as $key => $label): ?><option value="<?= e($key) ?>" <?= $activeStatus === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select>
        <button class="button button-secondary" type="submit">Filter</button>
        <?php if ($activeType !== '' || $activeStatus !== ''): ?><a class="button button-secondary" href="/cases">Reset</a><?php endif; ?>
    </form>
    <a class="button" href="/cases/create">New case</a>
</div>

<section class="panel">
    <?php if ($cases === []): ?>
        <div class="empty"><p>No cases in this view.</p><a class="button" href="/cases/create">Create case</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Case</th><th>Client and contacts</th><th>Assignee</th><th>Priority</th><th>Due date</th><th>Status</th><th></th></tr></thead>
            <tbody><?php foreach ($cases as $case): ?><tr>
                <td><a class="table-title" href="/cases/<?= e($case['id']) ?>/edit">#<?= e($case['id']) ?> <?= e($case['title']) ?></a><small><?= e($types[$case['kind']]) ?><?= $case['amount'] > 0 ? ' · ' . money($case['amount']) : '' ?></small></td>
                <td><?php if ($case['client_id']): ?><a href="/clients/<?= e($case['client_id']) ?>"><?= e($case['client_name']) ?></a><?php else: ?>—<?php endif; ?><small><?= e($case['contact_names'] ?: 'No linked contacts') ?></small></td>
                <td><?= e($case['assignee_name'] ?: 'Unassigned') ?></td>
                <td><span class="badge priority-<?= e($case['priority']) ?>"><?= e(\Crm\Repositories\CaseRepository::PRIORITIES[$case['priority']]) ?></span></td>
                <td><?= $case['due_date'] ? e(date('M j, Y', strtotime($case['due_date']))) : '—' ?></td>
                <td><form class="stage-form" method="post" action="/cases/<?= e($case['id']) ?>/status"><?= csrf_field() ?><select class="stage-select status-<?= e($case['status']) ?>" name="status" onchange="this.form.submit()" aria-label="Case status"><?php foreach ($statuses as $key => $label): ?><option value="<?= e($key) ?>" <?= $case['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><noscript><button class="button button-small" type="submit">Save</button></noscript></form></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/cases/<?= e($case['id']) ?>/edit">Edit</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
