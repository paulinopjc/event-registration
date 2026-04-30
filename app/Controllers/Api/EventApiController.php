<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\RegistrationModel;
use App\Models\TicketTypeModel;
use CodeIgniter\API\ResponseTrait;

class EventApiController extends BaseController
{
    use ResponseTrait;

    public function stats(int $eventId)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($eventId);

        if (!$event) {
            return $this->failNotFound('Event not found');
        }

        $regModel = new RegistrationModel();
        $ticketModel = new TicketTypeModel();

        $totalRegistrations = $regModel->where('event_id', $eventId)
            ->where('status !=', 'cancelled')
            ->countAllResults();

        $checkedIn = $regModel->where('event_id', $eventId)
            ->where('status', 'checked_in')
            ->countAllResults();

        $cancelled = $regModel->where('event_id', $eventId)
            ->where('status', 'cancelled')
            ->countAllResults();

        $tickets = $ticketModel->where('event_id', $eventId)->findAll();
        $counts = $regModel->countByTicketType($eventId);
        $countMap = [];
        foreach ($counts as $c) {
            $countMap[$c['ticket_type_id']] = (int) $c['count'];
        }

        $ticketStats = [];
        foreach ($tickets as $t) {
            $ticketStats[] = [
                'name' => $t['name'],
                'registered' => $countMap[$t['id']] ?? 0,
                'capacity' => $t['capacity'] ? (int) $t['capacity'] : null,
            ];
        }

        return $this->respond([
            'data' => [
                'event' => $event['name'],
                'total_registrations' => $totalRegistrations,
                'checked_in' => $checkedIn,
                'cancelled' => $cancelled,
                'tickets' => $ticketStats,
            ],
        ]);
    }
}