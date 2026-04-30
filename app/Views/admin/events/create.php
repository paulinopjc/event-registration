<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Create Event</h2>

<form action="/admin/events/create" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card mb-4">
        <div class="card-header">Event Details</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Event Name *</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= old('description') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date/Time</label>
                    <input type="datetime-local" name="event_date" class="form-control" value="<?= old('event_date') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date/Time</label>
                    <input type="datetime-local" name="event_end_date" class="form-control" value="<?= old('event_end_date') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Venue</label>
                <input type="text" name="venue" class="form-control" value="<?= old('venue') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Banner Image</label>
                <input type="file" name="banner_image" class="form-control" accept="image/*">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            Ticket Types
            <button type="button" class="btn btn-sm btn-outline-primary" id="addTicket">+ Add Ticket</button>
        </div>
        <div class="card-body" id="ticketContainer">
            <div class="row g-2 mb-2 ticket-row">
                <div class="col-md-5">
                    <input type="text" name="ticket_name[]" class="form-control" placeholder="Ticket name (e.g. General Admission)">
                </div>
                <div class="col-md-3">
                    <input type="number" name="ticket_price[]" class="form-control" placeholder="Price" step="0.01" min="0">
                </div>
                <div class="col-md-3">
                    <input type="number" name="ticket_capacity[]" class="form-control" placeholder="Capacity (blank = unlimited)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">&times;</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            Custom Fields
            <button type="button" class="btn btn-sm btn-outline-primary" id="addField">+ Add Field</button>
        </div>
        <div class="card-body" id="fieldContainer">
            <p class="text-muted small">Add custom questions for the registration form (e.g. T-shirt size, dietary needs).</p>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Create Event</button>
    <a href="/admin/events" class="btn btn-secondary">Cancel</a>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('addTicket').addEventListener('click', function() {
    const row = document.querySelector('.ticket-row').cloneNode(true);
    row.querySelectorAll('input').forEach(input => input.value = '');
    document.getElementById('ticketContainer').appendChild(row);
});

document.getElementById('addField').addEventListener('click', function() {
    const html = `
        <div class="row g-2 mb-2 field-row">
            <div class="col-md-4">
                <input type="text" name="field_label[]" class="form-control" placeholder="Field label">
            </div>
            <div class="col-md-3">
                <select name="field_type[]" class="form-select">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="dropdown">Dropdown</option>
                    <option value="checkbox">Checkbox</option>
                    <option value="radio">Radio</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="field_options[]" class="form-control" placeholder="Options (comma-separated)">
            </div>
            <div class="col-md-1">
                <select name="field_required[]" class="form-select">
                    <option value="0">Optional</option>
                    <option value="1">Required</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row">&times;</button>
            </div>
        </div>`;
    document.getElementById('fieldContainer').insertAdjacentHTML('beforeend', html);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.row').remove();
    }
});
</script>
<?= $this->endSection() ?>