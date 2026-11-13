<div class="page-actions"><p class="muted">The 200 most recent events recorded by the Activity Log plugin.</p><a class="button button-secondary" href="/plugins">Configure plugin</a></div>
<section class="panel">
    <?php if ($activities === []): ?>
        <div class="empty"><p>No events yet. Add a record or create a case.</p><a class="button" href="/cases/create">Add case</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Event</th><th>Description</th><th>Entity</th><th>Date</th></tr></thead>
            <tbody><?php foreach ($activities as $activity): ?><tr>
                <td><code><?= e($activity['event']) ?></code></td>
                <td class="white-space-normal"><?= e($activity['message']) ?></td>
                <td><?= e($activity['entity_type']) ?> #<?= e($activity['entity_id']) ?></td>
                <td><?= e(date('M j, Y g:i A', strtotime($activity['created_at']))) ?></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
