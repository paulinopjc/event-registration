<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Users</h2>
    <a href="/admin/users/create" class="btn btn-primary">Add User</a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= esc($user['name']) ?></td>
            <td><?= esc($user['email']) ?></td>
            <td>
                <?php
                    $roleBadge = match($user['role']) {
                        'admin'  => 'danger',
                        'editor' => 'info',
                        'viewer' => 'secondary',
                        default  => 'secondary',
                    };
                ?>
                <span class="badge bg-<?= $roleBadge ?>"><?= ucfirst($user['role']) ?></span>
            </td>
            <td>
                <?php if ($user['is_active']): ?>
                    <span class="badge bg-success">Active</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactive</span>
                <?php endif; ?>
            </td>
            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
            <td>
                <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
                <?php if ($user['id'] !== (int) session()->get('user_id')): ?>
                    <form action="/admin/users/<?= $user['id'] ?>/deactivate" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-<?= $user['is_active'] ? 'danger' : 'success' ?>">
                            <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
