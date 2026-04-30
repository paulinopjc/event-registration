<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\TicketTypeModel;
use App\Models\CustomFieldModel;
use CodeIgniter\I18n\Time;

class EventController extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $events = $eventModel->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/events/index', ['events' => $events]);
    }

    public function create()
    {
        return view('admin/events/create');
    }

    public function store()
    {
        $eventModel = new EventModel();

        $data = [
            'user_id' => session()->get('user_id'),
            'name' => $this->request->getPost('name'),
            'slug' => url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'venue' => $this->request->getPost('venue'),
            'event_date' => $this->request->getPost('event_date'),
            'event_end_date' => $this->request->getPost('event_end_date'),
            'status' => 'draft',
        ];

        // Handle banner upload
        $banner = $this->request->getFile('banner_image');
        if ($banner && $banner->isValid() && !$banner->hasMoved()) {
            $newName = $banner->getRandomName();
            $banner->move(FCPATH . 'uploads/banners', $newName);
            $data['banner_image'] = 'uploads/banners/' . $newName;
        }

        if (!$eventModel->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $eventModel->errors());
        }

        $eventId = $eventModel->getInsertID();

        // Save ticket types
        $ticketModel = new TicketTypeModel();
        $ticketNames = $this->request->getPost('ticket_name') ?? [];
        foreach ($ticketNames as $i => $name) {
            if (empty($name)) continue;
            $ticketModel->save([
                'event_id' => $eventId,
                'name' => $name,
                'price' => $this->request->getPost('ticket_price')[$i] ?? 0,
                'capacity' => $this->request->getPost('ticket_capacity')[$i] ?: null,
                'sort_order' => $i,
            ]);
        }

        // Save custom fields
        $fieldModel = new CustomFieldModel();
        $fieldLabels = $this->request->getPost('field_label') ?? [];
        foreach ($fieldLabels as $i => $label) {
            if (empty($label)) continue;
            $fieldModel->save([
                'event_id' => $eventId,
                'label' => $label,
                'field_type' => $this->request->getPost('field_type')[$i] ?? 'text',
                'is_required' => $this->request->getPost('field_required')[$i] ?? 0,
                'options' => $this->request->getPost('field_options')[$i] ? json_encode(explode(',', $this->request->getPost('field_options')[$i])) : null,
                'sort_order' => $i,
            ]);
        }

        return redirect()->to("/admin/events/{$eventId}")
            ->with('success', 'Event created successfully');
    }

    public function show(int $id)
    {
        $eventModel = new EventModel();
        $event = $eventModel->find($id);
        if (!$event) return redirect()->to('/admin/events');

        $registrationModel = new \App\Models\RegistrationModel();
        $attendees = $registrationModel->getForEvent($id, [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
        ]);

        $ticketModel = new TicketTypeModel();
        $tickets = $ticketModel->where('event_id', $id)->findAll();

        $counts = $registrationModel->countByTicketType($id);
        $countMap = [];
        foreach ($counts as $c) {
            $countMap[$c['ticket_type_id']] = $c['count'];
        }

        return view('admin/events/show', [
            'event' => $event,
            'attendees' => $attendees,
            'tickets' => $tickets,
            'countMap' => $countMap,
        ]);
    }

    public function publish(int $id)
    {
        $eventModel = new EventModel();
        $eventModel->update($id, ['status' => 'published']);
        return redirect()->back()->with('success', 'Event published');
    }

    public function close(int $id)
    {
        $eventModel = new EventModel();
        $eventModel->update($id, ['status' => 'closed']);
        return redirect()->back()->with('success', 'Registration closed');
    }
}