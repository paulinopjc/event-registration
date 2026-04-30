<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\EventModel;

class AttendeeController extends BaseController
{
    public function show(int $id)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->select('registrations.*, ticket_types.name as ticket_type_name, events.name as event_name')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->join('events', 'events.id = registrations.event_id')
            ->find($id);

        if (!$registration) {
            return redirect()->back()->with('error', 'Registration not found');
        }

        $fieldValueModel = new \App\Models\CustomFieldValueModel();
        $customValues = $fieldValueModel->getByRegistration($id);

        return view('admin/attendees/show', [
            'registration' => $registration,
            'customValues' => $customValues,
        ]);
    }

    public function checkin(int $id)
    {
        $regModel = new RegistrationModel();
        $regModel->update($id, [
            'status' => 'checked_in',
            'checked_in_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->back()->with('success', 'Attendee checked in');
    }

    public function cancel(int $id)
    {
        $regModel = new RegistrationModel();
        $regModel->update($id, ['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Registration cancelled');
    }

    public function resend(int $id)
    {
        $regModel = new RegistrationModel();
        $registration = $regModel->find($id);

        if (!$registration) {
            return redirect()->back()->with('error', 'Registration not found');
        }

        $eventModel = new EventModel();
        $event = $eventModel->find($registration['event_id']);

        $ticketModel = new \App\Models\TicketTypeModel();
        $ticket = $ticketModel->find($registration['ticket_type_id']);

        // Resend confirmation email
        $email = \Config\Services::email();
        $email->setTo($registration['email']);
        $email->setSubject("Registration Confirmed: {$event['name']}");

        $data = [
            'event' => $event,
            'code' => $registration['confirmation_code'],
            'ticket' => $ticket,
            'name' => $registration['first_name'] . ' ' . $registration['last_name'],
        ];

        $email->setMessage(view('emails/confirmation', $data));
        $email->setMailType('html');

        if ($registration['qr_code_path'] && file_exists($registration['qr_code_path'])) {
            $email->attach($registration['qr_code_path'], 'inline', $registration['confirmation_code'] . '.png', 'image/png');
        }

        $email->send();

        return redirect()->back()->with('success', 'Confirmation email resent');
    }

    public function export(int $eventId)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($eventId);

        if (!$event) {
            return redirect()->back()->with('error', 'Event not found');
        }

        $regModel = new RegistrationModel();
        $attendees = $regModel->select('registrations.*, ticket_types.name as ticket_type_name')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->where('registrations.event_id', $eventId)
            ->orderBy('registrations.created_at', 'ASC')
            ->findAll();

        $filename = url_title($event['name'], '-', true) . '-attendees-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'Confirmation Code', 'First Name', 'Last Name', 'Email',
            'Phone', 'Company', 'Ticket Type', 'Status',
            'Checked In At', 'Registered At',
        ]);

        // Data rows
        foreach ($attendees as $a) {
            fputcsv($output, [
                $a['confirmation_code'],
                $a['first_name'],
                $a['last_name'],
                $a['email'],
                $a['phone'] ?? '',
                $a['company'] ?? '',
                $a['ticket_type_name'],
                $a['status'],
                $a['checked_in_at'] ?? '',
                $a['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}