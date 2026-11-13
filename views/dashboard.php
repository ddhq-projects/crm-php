<div class="page-actions">
    <p class="muted">A shared workspace for leads, issues and tasks.</p>
    <div class="actions">
        <a class="button button-secondary" href="/contacts/create">Add contact</a>
        <a class="button" href="/cases/create">New case</a>
    </div>
</div>

<section class="stats" aria-label="Metrics">
    <article class="stat-card"><span>Open cases</span><strong><?= e($caseStats['open']) ?></strong><a href="/cases">All cases →</a></article>
    <article class="stat-card"><span>Unassigned</span><strong><?= e($caseStats['unassigned']) ?></strong><small>Open cases without an owner</small></article>
    <article class="stat-card"><span>Clients</span><strong><?= e($clientCount) ?></strong><a href="/clients">All clients →</a></article>
    <article class="stat-card"><span>Active users</span><strong><?= e($userCount) ?></strong><a href="/users">Manage users →</a></article>
</section>

<section class="panel">
    <div class="panel-heading"><h2>Case flow</h2><span class="muted">Cases by status</span></div>
    <div class="pipeline pipeline-five">
        <?php foreach ($statusSummary as $key => $status): ?>
            <a class="pipeline-stage status-<?= e($key) ?>" href="/cases?status=<?= e($key) ?>">
                <span><?= e($status['label']) ?></span>
                <strong><?= e($status['count']) ?></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Recent cases</h2><a href="/cases">All cases</a></div>
        <?php if ($recentCases === []): ?>
            <div class="empty"><p>No cases yet.</p><a class="button" href="/cases/create">Create the first case</a></div>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Case</th><th>Client</th><th>Assignee</th><th>Status</th></tr></thead>
                <tbody><?php foreach ($recentCases as $case): ?><tr>
                    <td><a class="table-title" href="/cases/<?= e($case['id']) ?>/edit">#<?= e($case['id']) ?> <?= e($case['title']) ?></a><small><?= e(\Crm\Repositories\CaseRepository::TYPES[$case['kind']]) ?></small></td>
                    <td><?= e($case['client_name'] ?: '—') ?></td>
                    <td><?= e($case['assignee_name'] ?: 'Unassigned') ?></td>
                    <td><span class="badge status-<?= e($case['status']) ?>"><?= e(\Crm\Repositories\CaseRepository::STATUSES[$case['status']]) ?></span></td>
                </tr><?php endforeach; ?></tbody>
            </table></div>
        <?php endif; ?>
    </section>

    <?php foreach ($widgets as $widget): ?>
        <section class="panel">
            <div class="panel-heading"><h2><?= e($widget['title']) ?></h2><?php if (!empty($widget['url'])): ?><a href="<?= e($widget['url']) ?>">View all</a><?php endif; ?></div>
            <?php if (empty($widget['items'])): ?>
                <p class="muted">Events appear after records are created or changed.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($widget['items'] as $item): ?><li><span><?= e($item['message']) ?></span><time><?= e(date('M j, g:i A', strtotime($item['created_at']))) ?></time></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
