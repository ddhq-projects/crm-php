<div class="page-actions">
    <form class="search-form" method="get" action="/contacts">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Name, client or email" aria-label="Search contacts">
        <button class="button button-secondary" type="submit">Search</button>
    </form>
    <a class="button" href="/contacts/create">Add contact</a>
</div>

<section class="panel">
    <?php if ($contacts === []): ?>
        <div class="empty"><p><?= $search === '' ? 'No contacts yet.' : 'Nothing matched this search.' ?></p><?php if ($search === ''): ?><a class="button" href="/contacts/create">Add the first contact</a><?php endif; ?></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Contact</th><th>Client</th><th>Communication</th><th>Cases</th><th></th></tr></thead>
            <tbody><?php foreach ($contacts as $contact): ?><tr>
                <td><a class="table-title" href="/contacts/<?= e($contact['id']) ?>/edit"><?= e($contact['name']) ?></a><small><?= e($contact['job_title']) ?></small></td>
                <td><?php if ($contact['client_id']): ?><a href="/clients/<?= e($contact['client_id']) ?>"><?= e($contact['client_name']) ?></a><small><?= e($contact['client_company']) ?></small><?php else: ?>—<?php endif; ?></td>
                <td><?= e($contact['email'] ?: '—') ?><small><?= e($contact['phone']) ?></small></td>
                <td><?= e($contact['cases_count']) ?></td>
                <td class="align-right"><a class="button button-small button-secondary" href="/contacts/<?= e($contact['id']) ?>/edit">Edit</a></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php endif; ?>
</section>
