<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Events</h5>
                <p class="display-6"><?= $totalEvents ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Published</h5>
                <p class="display-6"><?= $publishedEvents ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Registrations</h5>
                <p class="display-6"><?= $totalRegistrations ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Checked In</h5>
                <p class="display-6"><?= $checkedIn ?></p>
            </div>
        </div>
    </div>
</div>

<h4>Recent Events</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentEvents as $event): ?>
        <tr>
            <td><?= esc($event['name']) ?></td>
            <td><?= $event['event_date'] ? date('M d, Y', strtotime($event['event_date'])) : 'TBD' ?></td>
            <td>
                <span class="badge bg-<?= $event['status'] === 'published' ? 'success' : ($event['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                    <?= ucfirst($event['status']) ?>
                </span>
            </td>
            <td><a href="/admin/events/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentEvents)): ?>
        <tr><td colspan="4" class="text-muted text-center">No events yet. <a href="/admin/events/create">Create one.</a></td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>