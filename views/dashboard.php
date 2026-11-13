<div class="page-actions">
    <p class="muted">Key metrics and the current sales pipeline.</p>
    <div class="actions">
        <a class="button button-secondary" href="/clients/create">Add client</a>
        <a class="button" href="/deals/create">New deal</a>
    </div>
</div>

<section class="stats" aria-label="Metrics">
    <article class="stat-card"><span>Clients</span><strong><?= e($clientCount) ?></strong><a href="/clients">All clients →</a></article>
    <article class="stat-card"><span>Total deals</span><strong><?= e($dealStats['total']) ?></strong><a href="/deals">Open pipeline →</a></article>
    <article class="stat-card"><span>In progress</span><strong><?= money($dealStats['open_amount']) ?></strong><small>Value of active deals</small></article>
    <article class="stat-card"><span>Won</span><strong><?= money($dealStats['won_amount']) ?></strong><small>Total closed-won value</small></article>
</section>

<section class="panel">
    <div class="panel-heading"><h2>Sales pipeline</h2><span class="muted">Count / value</span></div>
    <div class="pipeline">
        <?php foreach ($pipeline as $key => $stage): ?>
            <a class="pipeline-stage stage-<?= e($key) ?>" href="/deals?stage=<?= e($key) ?>">
                <span><?= e($stage['label']) ?></span>
                <strong><?= e($stage['count']) ?></strong>
                <small><?= money($stage['amount']) ?></small>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-heading"><h2>Recent deals</h2><a href="/deals">All deals</a></div>
        <?php if ($recentDeals === []): ?>
            <div class="empty"><p>No deals yet.</p><a class="button" href="/deals/create">Create the first deal</a></div>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Deal</th><th>Client</th><th>Amount</th><th>Stage</th></tr></thead>
                <tbody><?php foreach ($recentDeals as $deal): ?><tr>
                    <td><a class="table-title" href="/deals/<?= e($deal['id']) ?>/edit"><?= e($deal['title']) ?></a></td>
                    <td><?= e($deal['client_name']) ?></td>
                    <td><?= money($deal['amount']) ?></td>
                    <td><span class="badge stage-<?= e($deal['stage']) ?>"><?= e(\Crm\Repositories\DealRepository::STAGES[$deal['stage']]) ?></span></td>
                </tr><?php endforeach; ?></tbody>
            </table></div>
        <?php endif; ?>
    </section>

    <?php foreach ($widgets as $widget): ?>
        <section class="panel">
            <div class="panel-heading"><h2><?= e($widget['title']) ?></h2><?php if (!empty($widget['url'])): ?><a href="<?= e($widget['url']) ?>">View all</a><?php endif; ?></div>
            <?php if (empty($widget['items'])): ?>
                <p class="muted">Events appear after clients and deals are created.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($widget['items'] as $item): ?><li><span><?= e($item['message']) ?></span><time><?= e(date('M j, g:i A', strtotime($item['created_at']))) ?></time></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
