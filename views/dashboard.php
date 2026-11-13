<div class="page-actions">
    <p class="muted">Главные показатели и текущая воронка продаж.</p>
    <div class="actions">
        <a class="button button-secondary" href="/clients/create">Добавить клиента</a>
        <a class="button" href="/deals/create">Новая сделка</a>
    </div>
</div>

<section class="stats" aria-label="Показатели">
    <article class="stat-card"><span>Клиенты</span><strong><?= e($clientCount) ?></strong><a href="/clients">Все клиенты →</a></article>
    <article class="stat-card"><span>Всего сделок</span><strong><?= e($dealStats['total']) ?></strong><a href="/deals">Открыть воронку →</a></article>
    <article class="stat-card"><span>В работе</span><strong><?= money($dealStats['open_amount']) ?></strong><small>Сумма активных сделок</small></article>
    <article class="stat-card"><span>Выиграно</span><strong><?= money($dealStats['won_amount']) ?></strong><small>Общая сумма успеха</small></article>
</section>

<section class="panel">
    <div class="panel-heading"><h2>Воронка продаж</h2><span class="muted">Количество / сумма</span></div>
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
        <div class="panel-heading"><h2>Недавние сделки</h2><a href="/deals">Все сделки</a></div>
        <?php if ($recentDeals === []): ?>
            <div class="empty"><p>Сделок пока нет.</p><a class="button" href="/deals/create">Создать первую сделку</a></div>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Сделка</th><th>Клиент</th><th>Сумма</th><th>Этап</th></tr></thead>
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
            <div class="panel-heading"><h2><?= e($widget['title']) ?></h2><?php if (!empty($widget['url'])): ?><a href="<?= e($widget['url']) ?>">Подробнее</a><?php endif; ?></div>
            <?php if (empty($widget['items'])): ?>
                <p class="muted">События появятся после создания клиентов и сделок.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($widget['items'] as $item): ?><li><span><?= e($item['message']) ?></span><time><?= e(date('d.m H:i', strtotime($item['created_at']))) ?></time></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
