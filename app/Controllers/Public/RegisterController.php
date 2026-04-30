<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\TicketTypeModel;
use App\Models\RegistrationModel;
use App\Models\CustomFieldModel;
use App\Models\CustomFieldValueModel;
use chillerlan\QRCode\QRCode;

class RegisterController extends BaseController
{
    public function event(string $slug)
    {
        $eventModel = new EventModel();
        $event = $eventModel->getBySlug($slug);

        if (!$event || $event['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ticketModel = new TicketTypeModel();
        $tickets = $ticketModel->where('event_id', $event['id'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->findAll();

        // Calculate remaining capacity
        $regModel = new RegistrationModel();
        $counts = $regModel->countByTicketType($event['id']);
        $countMap = [];
        foreach ($counts as $c) {
            $countMap[$c['ticket_type_id']] = $c['count'];
        }

        return view('public/event', [
            'event' => $event,
            'tickets' => $tickets,
            'countMap' => $countMap,
        ]);
    }

    public function register(string $slug)
    {
        $eventModel = new EventModel();
        $event = $eventModel->getBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $ticketModel = new TicketTypeModel();
        $tickets = $ticketModel->where('event_id', $event['id'])
            ->where('is_active', true)
            ->findAll();

        $fieldModel = new CustomFieldModel();
        $customFields = $fieldModel->where('event_id', $event['id'])
            ->orderBy('sort_order')
            ->findAll();

        return view('public/register', [
            'event' => $event,
            'tickets' => $tickets,
            'customFields' => $customFields,
        ]);
    }

    public function submit(string $slug)
    {
        $eventModel = new EventModel();
        $event = $eventModel->getBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Validate
        $rules = [
            'first_name' => 'required|max_length[255]',
            'last_name' => 'required|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'ticket_type_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check capacity
        $ticketTypeId = $this->request->getPost('ticket_type_id');
        $ticketModel = new TicketTypeModel();
        $ticket = $ticketModel->find($ticketTypeId);

        if ($ticket['capacity']) {
            $regModel = new RegistrationModel();
            $registered = $regModel->where('ticket_type_id', $ticketTypeId)
                ->where('status !=', 'cancelled')
                ->countAllResults();
            if ($registered >= $ticket['capacity']) {
                return redirect()->back()->withInput()
                    ->with('error', 'This ticket type is sold out');
            }
        }

        // Generate confirmation code
        $code = RegistrationModel::generateCode();
        while ((new RegistrationModel())->getByCode($code)) {
            $code = RegistrationModel::generateCode();
        }

        // Generate QR code
        $qrDir = WRITEPATH . 'qrcodes/';
        if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

        $qrImage = (new QRCode([
            'outputType' => 'png',
            'scale' => 10,
            'imageBase64' => false,
        ]))->render($code);
        $qrPath = $qrDir . $code . '.png';
        file_put_contents($qrPath, $qrImage);

        // Save registration
        $regModel = new RegistrationModel();
        $regModel->save([
            'event_id' => $event['id'],
            'ticket_type_id' => $ticketTypeId,
            'confirmation_code' => $code,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'company' => $this->request->getPost('company'),
            'qr_code_path' => $qrPath,
        ]);

        $registrationId = $regModel->getInsertID();

        // Save custom field values
        $fieldModel = new CustomFieldModel();
        $customFields = $fieldModel->where('event_id', $event['id'])->findAll();
        $valueModel = new CustomFieldValueModel();

        foreach ($customFields as $field) {
            $value = $this->request->getPost('custom_' . $field['id']);
            if ($value !== null) {
                $valueModel->save([
                    'registration_id' => $registrationId,
                    'custom_field_id' => $field['id'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        }

        // Send confirmation email
        $this->sendConfirmationEmail($event, $code, $ticket, $qrPath);

        return redirect()->to("/registration/{$code}");
    }

    private function sendConfirmationEmail(array $event, string $code, array $ticket, string $qrPath)
    {
        $email = \Config\Services::email();
        $email->setTo($this->request->getPost('email'));
        $email->setSubject("Registration Confirmed: {$event['name']}");

        $data = [
            'event' => $event,
            'code' => $code,
            'ticket' => $ticket,
            'name' => $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'),
        ];

        $email->setMessage(view('emails/confirmation', $data));
        $email->setMailType('html');
        $email->attach($qrPath, 'inline', $code . '.png', 'image/png');

        if (!$email->send(false)) {
            error_log('EMAIL FAILED: ' . $email->printDebugger(['headers', 'subject']));
        } else {
            error_log('EMAIL SENT OK to ' . $this->request->getPost('email'));
        }
    }

    public function confirmation(string $code)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->select('registrations.*, ticket_types.name as ticket_type_name, events.name as event_name, events.event_date, events.venue')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.confirmation_code', $code)
            ->first();

        if (!$registration) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('public/confirmation', ['registration' => $registration]);
    }
}