<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit User</h2>

<div class="row">
    <div class="col-md-6">
        <form action="/admin/users/<?= $user['id'] ?>/edit" method="post">
            <?= csrf_field() ?>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                        <div class="form-text">Email cannot be changed (tied to Google account).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-select" required <?= $user['id'] === (int) session()->get('user_id') ? 'disabled' : '' ?>>
                            <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer - View events, check in, export</option>
                            <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor - Create/edit events, manage registrations</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin - Full access including user management</option>
                        </select>
                        <?php if ($user['id'] === (int) session()->get('user_id')): ?>
                            <div class="form-text text-warning">You cannot change your own role.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update User</button>
            <a href="/admin/users" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
