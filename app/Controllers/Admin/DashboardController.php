<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\RegistrationModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $registrationModel = new RegistrationModel();

        $userId = session()->get('user_id');

        $totalEvents = $eventModel->where('user_id', $userId)->countAllResults();
        $publishedEvents = $eventModel->where('user_id', $userId)->where('status', 'published')->countAllResults();

        $eventIds = array_column(
            $eventModel->select('id')->where('user_id', $userId)->findAll(),
            'id'
        );

        $totalRegistrations = 0;
        $checkedIn = 0;
        if (!empty($eventIds)) {
            $totalRegistrations = $registrationModel->whereIn('event_id', $eventIds)
                ->where('status !=', 'cancelled')
                ->countAllResults();
            $checkedIn = $registrationModel->whereIn('event_id', $eventIds)
                ->where('status', 'checked_in')
                ->countAllResults();
        }

        $recentEvents = $eventModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('admin/dashboard', [
            'totalEvents' => $totalEvents,
            'publishedEvents' => $publishedEvents,
            'totalRegistrations' => $totalRegistrations,
            'checkedIn' => $checkedIn,
            'recentEvents' => $recentEvents,
        ]);
    }
}