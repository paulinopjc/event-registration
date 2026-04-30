<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
        <?= esc($event['name']) ?>
        <?php if ($event['is_restricted']): ?>
            <span class="badge bg-warning text-dark fs-6">Restricted</span>
        <?php endif; ?>
    </h2>
    <div>
        <?php if ($event['status'] === 'draft' && in_array(session()->get('user_role'), ['admin', 'editor'])): ?>
            <form action="/admin/events/<?= $event['id'] ?>/publish" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-success btn-sm">Publish</button>
            </form>
        <?php elseif ($event['status'] === 'published'): ?>
            <a href="/event/<?= $event['slug'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">View Public Page</a>
            <?php if (session()->get('user_role') === 'admin'): ?>
                <form action="/admin/events/<?= $event['id'] ?>/close" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-warning btn-sm">Close Registration</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/admin/events/<?= $event['id'] ?>/export" class="btn btn-outline-secondary btn-sm">Export CSV</a>
        <?php if ($event['is_restricted'] && in_array(session()->get('user_role'), ['admin', 'editor'])): ?>
            <a href="/admin/events/<?= $event['id'] ?>/guests" class="btn btn-outline-info btn-sm">Guest List</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($event['is_restricted'] && $event['status'] === 'published'): ?>
    <div class="alert alert-info">
        <strong>Restricted Event</strong> - Share this link with invitees:
        <code><?= base_url("event/{$event['slug']}") ?></code>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <p><strong>Date:</strong> <?= $event['event_date'] ? date('M d, Y H:i', strtotime($event['event_date'])) : 'TBD' ?></p>
        <p><strong>Venue:</strong> <?= esc($event['venue'] ?? 'Not set') ?></p>
        <p><strong>Status:</strong>
            <span class="badge bg-<?= $event['status'] === 'published' ? 'success' : ($event['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                <?= ucfirst($event['status']) ?>
            </span>
        </p>
    </div>
    <div class="col-md-4">
        <h5>Ticket Types</h5>
        <?php foreach ($tickets as $ticket): ?>
            <p class="mb-1">
                <?= esc($ticket['name']) ?>:
                <?= $countMap[$ticket['id']] ?? 0 ?><?= $ticket['capacity'] ? '/' . $ticket['capacity'] : '' ?>
                registered
            </p>
        <?php endforeach; ?>
    </div>
</div>

<h4>Attendees (<?= count($attendees) ?>)</h4>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= esc($this->request ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="checked_in">Checked In</option>
            <option value="cancelled">Cancelled</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Filter</button>
    </div>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Email</th>
            <th>Ticket</th>
            <th>Status</th>
            <th>Registered</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendees as $a): ?>
        <tr>
            <td><code><?= $a['confirmation_code'] ?></code></td>
            <td><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></td>
            <td><?= esc($a['email']) ?></td>
            <td><?= esc($a['ticket_type_name'] ?? '') ?></td>
            <td>
                <?php
                    $badgeClass = match($a['status']) {
                        'checked_in' => 'success',
                        'confirmed'  => 'primary',
                        'pending'    => 'warning',
                        'rejected'   => 'dark',
                        'cancelled'  => 'danger',
                        default      => 'secondary',
                    };
                ?>
                <span class="badge bg-<?= $badgeClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $a['status'])) ?>
                </span>
            </td>
            <td><?= date('M d H:i', strtotime($a['created_at'])) ?></td>
            <td>
                <?php if ($a['status'] === 'pending' && in_array(session()->get('user_role'), ['admin', 'editor'])): ?>
                    <form action="/admin/registrations/<?= $a['id'] ?>/approve" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-success">Approve</button>
                    </form>
                    <form action="/admin/registrations/<?= $a['id'] ?>/reject" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-dark">Reject</button>
                    </form>
                <?php endif; ?>
                <?php if ($a['status'] === 'confirmed'): ?>
                    <form action="/admin/attendees/<?= $a['id'] ?>/checkin" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-success">Check In</button>
                    </form>
                    <?php if (session()->get('user_role') === 'admin'): ?>
                        <form action="/admin/attendees/<?= $a['id'] ?>/cancel" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (in_array($a['status'], ['confirmed', 'checked_in']) && in_array(session()->get('user_role'), ['admin', 'editor'])): ?>
                    <form action="/admin/attendees/<?= $a['id'] ?>/resend" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-secondary">Resend</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($attendees)): ?>
        <tr><td colspan="7" class="text-muted text-center">No registrations yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
