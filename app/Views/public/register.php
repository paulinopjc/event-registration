<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - <?= esc($event['name']) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <h2 class="mb-1"><?= esc($event['name']) ?></h2>
                    <p class="text-muted mb-4">Registration Form</p>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                    <li><?= $err ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/event/<?= $event['slug'] ?>/register" method="post">
                        <?= csrf_field() ?>

                        <div class="card mb-3">
                            <div class="card-header">Personal Information</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Company</label>
                                        <input type="text" name="company" class="form-control" value="<?= old('company') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">Select Ticket *</div>
                            <div class="card-body">
                                <?php foreach ($tickets as $ticket): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="ticket_type_id"
                                            value="<?= $ticket['id'] ?>" id="ticket_<?= $ticket['id'] ?>"
                                            <?= old('ticket_type_id') == $ticket['id'] ? 'checked' : '' ?> required>
                                        <label class="form-check-label" for="ticket_<?= $ticket['id'] ?>">
                                            <strong><?= esc($ticket['name']) ?></strong>
                                            (<?= $ticket['price'] > 0 ? '₱' . number_format($ticket['price'], 2) : 'Free' ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if (!empty($customFields)): ?>
                        <div class="card mb-3">
                            <div class="card-header">Additional Information</div>
                            <div class="card-body">
                                <?php foreach ($customFields as $field): ?>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <?= esc($field['label']) ?>
                                            <?= $field['is_required'] ? '*' : '' ?>
                                        </label>

                                        <?php if ($field['field_type'] === 'text'): ?>
                                            <input type="text" name="custom_<?= $field['id'] ?>" class="form-control"
                                                <?= $field['is_required'] ? 'required' : '' ?>>

                                        <?php elseif ($field['field_type'] === 'textarea'): ?>
                                            <textarea name="custom_<?= $field['id'] ?>" class="form-control" rows="3"
                                                    <?= $field['is_required'] ? 'required' : '' ?>></textarea>

                                        <?php elseif ($field['field_type'] === 'dropdown'): ?>
                                            <select name="custom_<?= $field['id'] ?>" class="form-select"
                                                    <?= $field['is_required'] ? 'required' : '' ?>>
                                                <option value="">Select...</option>
                                                <?php foreach (json_decode($field['options'] ?? '[]') as $opt): ?>
                                                    <option value="<?= esc(trim($opt)) ?>"><?= esc(trim($opt)) ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                        <?php elseif ($field['field_type'] === 'checkbox'): ?>
                                            <?php foreach (json_decode($field['options'] ?? '[]') as $opt): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="custom_<?= $field['id'] ?>[]" value="<?= esc(trim($opt)) ?>">
                                                    <label class="form-check-label"><?= esc(trim($opt)) ?></label>
                                                </div>
                                            <?php endforeach; ?>

                                        <?php elseif ($field['field_type'] === 'radio'): ?>
                                            <?php foreach (json_decode($field['options'] ?? '[]') as $opt): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="custom_<?= $field['id'] ?>" value="<?= esc(trim($opt)) ?>"
                                                        <?= $field['is_required'] ? 'required' : '' ?>>
                                                    <label class="form-check-label"><?= esc(trim($opt)) ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Complete Registration</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>