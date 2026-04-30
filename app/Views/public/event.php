<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($event['name']) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php if ($event['banner_image']): ?>
            <img src="/<?= $event['banner_image'] ?>" class="w-100" style="max-height: 300px; object-fit: cover;" alt="Event banner">
        <?php endif; ?>

        <div class="container py-5">
            <h1><?= esc($event['name']) ?></h1>

            <div class="row mt-4">
                <div class="col-md-8">
                    <p class="text-muted">
                        <i class="bi bi-calendar"></i>
                        <?= $event['event_date'] ? date('F d, Y \a\t h:i A', strtotime($event['event_date'])) : 'Date TBD' ?>
                        <?php if ($event['event_end_date']): ?>
                            to <?= date('F d, Y \a\t h:i A', strtotime($event['event_end_date'])) ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($event['venue']): ?>
                        <p class="text-muted"><i class="bi bi-geo-alt"></i> <?= esc($event['venue']) ?></p>
                    <?php endif; ?>

                    <div class="mt-4"><?= $event['description'] ?></div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Tickets</h5></div>
                        <div class="card-body">
                            <?php foreach ($tickets as $ticket): ?>
                                <?php
                                    $registered = $countMap[$ticket['id']] ?? 0;
                                    $soldOut = $ticket['capacity'] && $registered >= $ticket['capacity'];
                                ?>
                                <div class="mb-3">
                                    <strong><?= esc($ticket['name']) ?></strong>
                                    <span class="float-end">
                                        <?= $ticket['price'] > 0 ? '₱' . number_format($ticket['price'], 2) : 'Free' ?>
                                    </span>
                                    <?php if ($ticket['description']): ?>
                                        <br><small class="text-muted"><?= esc($ticket['description']) ?></small>
                                    <?php endif; ?>
                                    <?php if ($soldOut): ?>
                                        <br><span class="badge bg-danger">Sold Out</span>
                                    <?php elseif ($ticket['capacity']): ?>
                                        <br><small class="text-muted"><?= $ticket['capacity'] - $registered ?> spots left</small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <a href="/event/<?= $event['slug'] ?>/register" class="btn btn-primary w-100 mt-2">Register Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>