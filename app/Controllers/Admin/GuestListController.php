<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\GuestListModel;
use App\Models\RegistrationModel;
use App\Models\TicketTypeModel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRGdImagePNG;

class GuestListController extends BaseController
{
    public function index(int $eventId)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($eventId);

        if (!$event || !$event['is_restricted']) {
            return redirect()->to('/admin/events')->with('error', 'Event not found or not restricted');
        }

        $guestModel = new GuestListModel();
        $guests = $guestModel->getForEvent($eventId);

        $regModel = new RegistrationModel();
        $pendingRegistrations = $regModel->select('registrations.*, ticket_types.name as ticket_type_name')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->where('registrations.event_id', $eventId)
            ->where('registrations.status', 'pending')
            ->orderBy('registrations.created_at', 'DESC')
            ->findAll();

        return view('admin/events/guests', [
            'event' => $event,
            'guests' => $guests,
            'pendingRegistrations' => $pendingRegistrations,
        ]);
    }

    public function upload(int $eventId)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($eventId);

        if (!$event || !$event['is_restricted']) {
            return redirect()->to('/admin/events')->with('error', 'Event not found or not restricted');
        }

        $file = $this->request->getFile('guest_csv');

        if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Please upload a valid CSV file');
        }

        $handle = fopen($file->getTempName(), 'r');

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'CSV file is empty');
        }

        // Normalize header names
        $header = array_map(function ($col) {
            return strtolower(trim($col));
        }, $header);

        $firstNameIdx = array_search('first_name', $header);
        $lastNameIdx = array_search('last_name', $header);
        $emailIdx = array_search('email', $header);

        if ($firstNameIdx === false || $lastNameIdx === false || $emailIdx === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'CSV must have columns: first_name, last_name, email');
        }

        $guestModel = new GuestListModel();
        $added = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $email = trim($row[$emailIdx] ?? '');
            $firstName = trim($row[$firstNameIdx] ?? '');
            $lastName = trim($row[$lastNameIdx] ?? '');

            if (empty($email) || empty($firstName)) {
                $skipped++;
                continue;
            }

            // Skip if already on the guest list
            $existing = $guestModel->findByEmail($eventId, $email);
            if ($existing) {
                $skipped++;
                continue;
            }

            $guestModel->save([
                'event_id' => $eventId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
            ]);
            $added++;
        }

        fclose($handle);

        return redirect()->back()->with('success', "{$added} guests added, {$skipped} skipped (duplicates or invalid)");
    }

    public function delete(int $eventId, int $guestId)
    {
        $guestModel = new GuestListModel();
        $guest = $guestModel->find($guestId);

        if (!$guest || $guest['event_id'] !== $eventId) {
            return redirect()->back()->with('error', 'Guest not found');
        }

        if ($guest['is_registered']) {
            return redirect()->back()->with('error', 'Cannot remove a guest who has already registered');
        }

        $guestModel->delete($guestId);
        return redirect()->back()->with('success', 'Guest removed');
    }

    public function approve(int $registrationId)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->find($registrationId);

        if (!$registration || $registration['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Registration not found or not pending');
        }

        // Generate QR code
        $code = $registration['confirmation_code'];
        $qrDir = WRITEPATH . 'qrcodes/';
        if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

        $qrDataUri = (new QRCode([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 10,
        ]))->render($code);

        $qrBase64 = explode(',', $qrDataUri, 2)[1];
        $qrPath = $qrDir . $code . '.png';
        file_put_contents($qrPath, base64_decode($qrBase64));

        // Update registration
        $regModel->update($registrationId, [
            'status' => 'confirmed',
            'qr_code_path' => $qrPath,
        ]);

        // Mark guest as registered if on guest list
        $guestModel = new GuestListModel();
        $guest = $guestModel->findByEmail($registration['event_id'], $registration['email']);
        if ($guest) {
            $guestModel->markRegistered($guest['id'], $registrationId);
        }

        // Send confirmation email
        $eventModel = new EventModel();
        $event = $eventModel->find($registration['event_id']);
        $ticketModel = new TicketTypeModel();
        $ticket = $ticketModel->find($registration['ticket_type_id']);

        $this->sendConfirmationEmail($event, $registration, $ticket, $qrBase64);

        return redirect()->back()->with('success', 'Registration approved and confirmation email sent');
    }

    public function reject(int $registrationId)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->find($registrationId);

        if (!$registration || $registration['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Registration not found or not pending');
        }

        $regModel->update($registrationId, ['status' => 'rejected']);

        return redirect()->back()->with('success', 'Registration rejected');
    }

    private function sendConfirmationEmail(array $event, array $registration, array $ticket, string $qrBase64)
    {
        $recipientName = $registration['first_name'] . ' ' . $registration['last_name'];

        $htmlContent = view('emails/confirmation', [
            'event' => $event,
            'code' => $registration['confirmation_code'],
            'ticket' => $ticket,
            'name' => $recipientName,
        ]);

        $payload = [
            'sender' => [
                'name' => getenv('EMAIL_FROM_NAME') ?: 'Event Platform',
                'email' => getenv('EMAIL_FROM') ?: 'noreply@events.test',
            ],
            'to' => [['email' => $registration['email'], 'name' => $recipientName]],
            'subject' => "Registration Confirmed: {$event['name']}",
            'htmlContent' => $htmlContent,
            'attachment' => [[
                'content' => $qrBase64,
                'name' => $registration['confirmation_code'] . '.png',
            ]],
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'api-key: ' . getenv('BREVO_API_KEY'),
                'content-type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            error_log("BREVO API FAILED (HTTP $httpCode): $response");
        }
    }
}
