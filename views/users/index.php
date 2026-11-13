<div class="page-actions">
    <p class="muted">Administrators, team members and client users in one directory.</p>
    <a class="button" href="/users/create">Add user</a>
</div>

<section class="panel">
    <?php if ($users === []): ?>
        <div class="empty"><p>No users yet.</p><a class="button" href="/users/create">Add the first user</a></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>User</th><th>Role</th><th>State</th><th>Assigned cases</th><th></th></tr></thead>
            <tbody><?php foreach ($users as $user): ?><tr>
                <td><a class="table-title" href="/users/<?= e($user['id']) ?>/edit"><?= e($user['name']) ?></a><small><?= e($user['email']) ?></small></td>
                <td><?= e($roles[$user['role']]) ?></td>
                <td><span class="badge <?= $user['active'] ? 'badge-success' : '' ?>"><?= $user['active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><?= e($user['assigned_cases_count']) ?></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/users/<?= e($user['id']) ?>/edit">Edit</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
