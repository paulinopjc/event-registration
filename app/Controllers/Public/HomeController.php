<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\EventModel;

class HomeController extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $events = $eventModel->getPublished();

        return view('public/home', ['events' => $events]);
    }
}
