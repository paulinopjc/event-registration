<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Add User</h2>

<div class="row">
    <div class="col-md-6">
        <form action="/admin/users/create" method="post">
            <?= csrf_field() ?>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        <div class="form-text">The user will sign in with this Google account.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="viewer" <?= old('role') === 'viewer' ? 'selected' : '' ?>>Viewer - View events, check in, export</option>
                            <option value="editor" <?= old('role') === 'editor' ? 'selected' : '' ?>>Editor - Create/edit events, manage registrations</option>
                            <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin - Full access including user management</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Add User</button>
            <a href="/admin/users" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
