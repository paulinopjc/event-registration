<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?= esc($event['name']) ?></h2>
    <div>
        <?php if ($event['status'] === 'draft'): ?>
            <form action="/admin/events/<?= $event['id'] ?>/publish" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-success btn-sm">Publish</button>
            </form>
        <?php elseif ($event['status'] === 'published'): ?>
            <a href="/event/<?= $event['slug'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">View Public Page</a>
            <form action="/admin/events/<?= $event['id'] ?>/close" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-warning btn-sm">Close Registration</button>
            </form>
        <?php endif; ?>
        <a href="/admin/events/<?= $event['id'] ?>/export" class="btn btn-outline-secondary btn-sm">Export CSV</a>
    </div>
</div>

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
            <option value="confirmed">Confirmed</option>
            <option value="checked_in">Checked In</option>
            <option value="cancelled">Cancelled</option>
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
                <span class="badge bg-<?= $a['status'] === 'checked_in' ? 'success' : ($a['status'] === 'cancelled' ? 'danger' : 'primary') ?>">
                    <?= ucfirst(str_replace('_', ' ', $a['status'])) ?>
                </span>
            </td>
            <td><?= date('M d H:i', strtotime($a['created_at'])) ?></td>
            <td>
                <?php if ($a['status'] === 'confirmed'): ?>
                    <form action="/admin/attendees/<?= $a['id'] ?>/checkin" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-success">Check In</button>
                    </form>
                    <form action="/admin/attendees/<?= $a['id'] ?>/cancel" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                <?php endif; ?>
                <form action="/admin/attendees/<?= $a['id'] ?>/resend" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-secondary">Resend</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($attendees)): ?>
        <tr><td colspan="7" class="text-muted text-center">No registrations yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>