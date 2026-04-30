<!DOCTYPE html>
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9fafb; }
            .code { font-size: 24px; font-weight: bold; text-align: center; padding: 15px; background: white; border: 2px dashed #2563eb; margin: 15px 0; }
            .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2><?= esc($event['name']) ?></h2>
            </div>
            <div class="content">
                <p>Hello <?= esc($name) ?>,</p>
                <p>Your registration has been confirmed!</p>

                <div class="code"><?= $code ?></div>

                <p><strong>Ticket:</strong> <?= esc($ticket['name']) ?></p>
                <p><strong>Date:</strong> <?= $event['event_date'] ? date('F d, Y \a\t h:i A', strtotime($event['event_date'])) : 'TBD' ?></p>
                <?php if ($event['venue']): ?>
                    <p><strong>Venue:</strong> <?= esc($event['venue']) ?></p>
                <?php endif; ?>

                <p>Your QR code is attached to this email. Present it at the venue for check-in.</p>
            </div>
            <div class="footer">
                <p>This is an automated message. Please do not reply.</p>
            </div>
        </div>
    </body>
</html>