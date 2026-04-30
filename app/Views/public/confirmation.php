<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Confirmed</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card">
                        <div class="card-body p-5">
                            <h2 class="text-success mb-3">Registration Confirmed!</h2>
                            <p class="lead"><?= esc($registration['event_name']) ?></p>

                            <div class="my-4">
                                <p><strong>Confirmation Code:</strong></p>
                                <h3><code><?= $registration['confirmation_code'] ?></code></h3>
                            </div>

                            <p><strong>Name:</strong> <?= esc($registration['first_name'] . ' ' . $registration['last_name']) ?></p>
                            <p><strong>Ticket:</strong> <?= esc($registration['ticket_type_name']) ?></p>
                            <p><strong>Date:</strong> <?= date('F d, Y \a\t h:i A', strtotime($registration['event_date'])) ?></p>
                            <?php if ($registration['venue']): ?>
                                <p><strong>Venue:</strong> <?= esc($registration['venue']) ?></p>
                            <?php endif; ?>

                            <p class="text-muted mt-4">A confirmation email with your QR code has been sent to your email address. Present the QR code at the venue for check-in.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>