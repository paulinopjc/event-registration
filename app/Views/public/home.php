<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Events</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">Event Platform</a>
                <?php if (session()->get('logged_in')): ?>
                    <a href="/admin/dashboard" class="btn btn-outline-light btn-sm">Admin</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline-light btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="container py-5">
            <h1 class="mb-4">Upcoming Events</h1>

            <?php if (empty($events)): ?>
                <p class="text-muted">No events available at the moment.</p>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($events as $event): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <?php if ($event['banner_image']): ?>
                                <img src="/<?= $event['banner_image'] ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= esc($event['name']) ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="bi bi-calendar-event text-white" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= esc($event['name']) ?></h5>
                                <p class="card-text text-muted small mb-1">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= $event['event_date'] ? date('F d, Y \a\t h:i A', strtotime($event['event_date'])) : 'Date TBD' ?>
                                </p>
                                <?php if ($event['venue']): ?>
                                    <p class="card-text text-muted small mb-2">
                                        <i class="bi bi-geo-alt me-1"></i><?= esc($event['venue']) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($event['description']): ?>
                                    <p class="card-text small"><?= esc(mb_strimwidth(strip_tags($event['description']), 0, 120, '...')) ?></p>
                                <?php endif; ?>
                                <a href="/event/<?= $event['slug'] ?>" class="btn btn-primary mt-auto">View Event</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
