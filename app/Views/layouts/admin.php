<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?? 'Admin' ?> - Event Platform</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/admin/dashboard">Event Platform</a>
                <div class="d-flex align-items-center">
                    <span class="text-light me-2"><?= session()->get('user_name') ?></span>
                    <span class="badge bg-<?= session()->get('user_role') === 'admin' ? 'danger' : (session()->get('user_role') === 'editor' ? 'info' : 'secondary') ?> me-3"><?= ucfirst(session()->get('user_role')) ?></span>
                    <a href="/logout" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            <!-- Sidebar -->
            <div class="bg-light border-end" style="width: 220px; min-height: calc(100vh - 56px);">
                <div class="p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/events"><i class="bi bi-calendar-event me-2"></i>Events</a>
                        </li>
                        <?php if (in_array(session()->get('user_role'), ['admin', 'editor'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/events/create"><i class="bi bi-plus-circle me-2"></i>Create Event</a>
                        </li>
                        <?php endif; ?>
                        <?php if (session()->get('user_role') === 'admin'): ?>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="/admin/users"><i class="bi bi-people me-2"></i>Users</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-grow-1 p-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <?= $this->renderSection('scripts') ?>
    </body>
</html>
