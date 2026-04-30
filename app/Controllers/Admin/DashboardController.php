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

        $totalEvents = $eventModel->countAllResults();
        $publishedEvents = $eventModel->where('status', 'published')->countAllResults();

        $totalRegistrations = $registrationModel->where('status !=', 'cancelled')
            ->where('status !=', 'rejected')
            ->countAllResults();
        $checkedIn = $registrationModel->where('status', 'checked_in')->countAllResults();

        $recentEvents = $eventModel->orderBy('created_at', 'DESC')
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
