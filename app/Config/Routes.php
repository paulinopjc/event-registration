<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', static function () {
    if (session()->get('logged_in')) {
        return redirect()->to('/admin/dashboard');
    }
    return redirect()->to('/login');
});

// Auth
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/login', 'Auth\LoginController::login');
$routes->get('logout', 'Auth\LoginController::logout');

// Public
$routes->get('event/(:segment)', 'Public\RegisterController::event/$1');
$routes->get('event/(:segment)/register', 'Public\RegisterController::register/$1');
$routes->post('event/(:segment)/register', 'Public\RegisterController::submit/$1');
$routes->get('registration/(:segment)', 'Public\RegisterController::confirmation/$1');

// Admin (protected by filter)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->get('events', 'Admin\EventController::index');
    $routes->get('events/create', 'Admin\EventController::create');
    $routes->post('events/create', 'Admin\EventController::store');
    $routes->get('events/(:num)/edit', 'Admin\EventController::edit/$1');
    $routes->post('events/(:num)/edit', 'Admin\EventController::update/$1');
    $routes->get('events/(:num)', 'Admin\EventController::show/$1');
    $routes->post('events/(:num)/publish', 'Admin\EventController::publish/$1');
    $routes->post('events/(:num)/close', 'Admin\EventController::close/$1');
    $routes->get('events/(:num)/export', 'Admin\AttendeeController::export/$1');

    $routes->get('attendees/(:num)', 'Admin\AttendeeController::show/$1');
    $routes->post('attendees/(:num)/cancel', 'Admin\AttendeeController::cancel/$1');
    $routes->post('attendees/(:num)/checkin', 'Admin\AttendeeController::checkin/$1');
    $routes->post('attendees/(:num)/resend', 'Admin\AttendeeController::resend/$1');
});

// API (for check-in integration)
$routes->group('api', function ($routes) {
    $routes->get('registrations/(:segment)', 'Api\RegistrationApiController::show/$1');
    $routes->post('registrations/(:segment)/checkin', 'Api\RegistrationApiController::checkin/$1');
    $routes->get('events/(:num)/stats', 'Api\EventApiController::stats/$1');
});