<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Events</h2>
    <a href="/admin/events/create" class="btn btn-primary">Create Event</a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Registrations</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr>
            <td><?= esc($event['name']) ?></td>
            <td><?= $event['event_date'] ? date('M d, Y H:i', strtotime($event['event_date'])) : 'TBD' ?></td>
            <td>
                <span class="badge bg-<?= $event['status'] === 'published' ? 'success' : ($event['status'] === 'draft' ? 'secondary' : 'danger') ?>">
                    <?= ucfirst($event['status']) ?>
                </span>
            </td>
            <td>-</td>
            <td>
                <a href="/admin/events/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                <a href="/admin/events/<?= $event['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($events)): ?>
        <tr><td colspan="5" class="text-muted text-center">No events yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>