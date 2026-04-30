<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Guest List: <?= esc($event['name']) ?></h2>
    <a href="/admin/events/<?= $event['id'] ?>" class="btn btn-outline-secondary btn-sm">Back to Event</a>
</div>

<?php
    $totalGuests = count($guests);
    $registeredGuests = count(array_filter($guests, fn($g) => $g['is_registered']));
?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4><?= $totalGuests ?></h4>
                <small class="text-muted">Total Invited</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4><?= $registeredGuests ?></h4>
                <small class="text-muted">Registered</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4><?= $totalGuests - $registeredGuests ?></h4>
                <small class="text-muted">Not Yet Registered</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4><?= count($pendingRegistrations) ?></h4>
                <small class="text-muted">Pending Approval</small>
            </div>
        </div>
    </div>
</div>

<!-- Upload CSV -->
<div class="card mb-4">
    <div class="card-header">Upload Guest List (CSV)</div>
    <div class="card-body">
        <form action="/admin/events/<?= $event['id'] ?>/guests/upload" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <input type="file" name="guest_csv" class="form-control" accept=".csv" required>
                <div class="form-text">CSV with columns: first_name, last_name, email</div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Pending Registrations -->
<?php if (!empty($pendingRegistrations)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">Pending Registrations</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Ticket</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingRegistrations as $reg): ?>
                <tr>
                    <td><?= esc($reg['first_name'] . ' ' . $reg['last_name']) ?></td>
                    <td><?= esc($reg['email']) ?></td>
                    <td><?= esc($reg['ticket_type_name'] ?? '') ?></td>
                    <td><?= date('M d H:i', strtotime($reg['created_at'])) ?></td>
                    <td>
                        <form action="/admin/registrations/<?= $reg['id'] ?>/approve" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form action="/admin/registrations/<?= $reg['id'] ?>/reject" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-dark">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Guest List -->
<div class="card">
    <div class="card-header">Invited Guests (<?= $totalGuests ?>)</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guests as $guest): ?>
                <tr>
                    <td><?= esc($guest['first_name'] . ' ' . $guest['last_name']) ?></td>
                    <td><?= esc($guest['email']) ?></td>
                    <td>
                        <?php if ($guest['is_registered']): ?>
                            <span class="badge bg-success">Registered</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Not Registered</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$guest['is_registered']): ?>
                            <form action="/admin/events/<?= $event['id'] ?>/guests/<?= $guest['id'] ?>/delete" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($guests)): ?>
                <tr><td colspan="4" class="text-muted text-center">No guests added yet. Upload a CSV to get started.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
