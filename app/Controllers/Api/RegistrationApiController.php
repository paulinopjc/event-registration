<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use CodeIgniter\API\ResponseTrait;

class RegistrationApiController extends BaseController
{
    use ResponseTrait;

    public function show(string $code)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->select('registrations.*, ticket_types.name as ticket_type_name, events.name as event_name')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.confirmation_code', $code)
            ->first();

        if (!$registration) {
            return $this->failNotFound('Registration not found');
        }

        return $this->respond([
            'data' => [
                'confirmation_code' => $registration['confirmation_code'],
                'name' => $registration['first_name'] . ' ' . $registration['last_name'],
                'email' => $registration['email'],
                'ticket_type' => $registration['ticket_type_name'],
                'event' => $registration['event_name'],
                'status' => $registration['status'],
                'checked_in_at' => $registration['checked_in_at'],
            ],
        ]);
    }

    public function checkin(string $code)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->where('confirmation_code', $code)->first();

        if (!$registration) {
            return $this->failNotFound('Registration not found');
        }

        if ($registration['status'] === 'checked_in') {
            return $this->fail('Already checked in at ' . $registration['checked_in_at'], 409);
        }

        if ($registration['status'] === 'cancelled') {
            return $this->fail('Registration is cancelled', 400);
        }

        $regModel->update($registration['id'], [
            'status' => 'checked_in',
            'checked_in_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'message' => 'Checked in successfully',
            'data' => [
                'confirmation_code' => $code,
                'checked_in_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}