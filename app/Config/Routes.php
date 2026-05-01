<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Public\HomeController::index');

// Auth
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/login', 'Auth\LoginController::login');
$routes->get('logout', 'Auth\LoginController::logout');

// Public
$routes->get('event/(:segment)', 'Public\RegisterController::event/$1');
$routes->get('event/(:segment)/register', 'Public\RegisterController::register/$1');
$routes->post('event/(:segment)/register', 'Public\RegisterController::submit/$1');
$routes->get('registration/(:segment)', 'Public\RegisterController::confirmation/$1');

// Admin - All authenticated users (admin, editor, viewer)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->get('events', 'Admin\EventController::index');
    $routes->get('events/(:num)', 'Admin\EventController::show/$1');
    $routes->get('events/(:num)/export', 'Admin\AttendeeController::export/$1');

    $routes->get('attendees/(:num)', 'Admin\AttendeeController::show/$1');
    $routes->post('attendees/(:num)/checkin', 'Admin\AttendeeController::checkin/$1');
});

// Admin - Admin + Editor only
$routes->group('admin', ['filter' => 'admin:admin,editor'], function ($routes) {
    $routes->get('events/create', 'Admin\EventController::create');
    $routes->post('events/create', 'Admin\EventController::store');
    $routes->get('events/(:num)/edit', 'Admin\EventController::edit/$1');
    $routes->post('events/(:num)/edit', 'Admin\EventController::update/$1');
    $routes->post('events/(:num)/publish', 'Admin\EventController::publish/$1');

    $routes->post('attendees/(:num)/resend', 'Admin\AttendeeController::resend/$1');

    // Guest list management
    $routes->get('events/(:num)/guests', 'Admin\GuestListController::index/$1');
    $routes->post('events/(:num)/guests/upload', 'Admin\GuestListController::upload/$1');
    $routes->post('events/(:num)/guests/(:num)/delete', 'Admin\GuestListController::delete/$1/$2');
    $routes->post('registrations/(:num)/approve', 'Admin\GuestListController::approve/$1');
    $routes->post('registrations/(:num)/reject', 'Admin\GuestListController::reject/$1');
});

// Admin - Admin only
$routes->group('admin', ['filter' => 'admin:admin'], function ($routes) {
    $routes->post('events/(:num)/close', 'Admin\EventController::close/$1');
    $routes->post('attendees/(:num)/cancel', 'Admin\AttendeeController::cancel/$1');

    // User management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/create', 'Admin\UserController::store');
    $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
    $routes->post('users/(:num)/edit', 'Admin\UserController::update/$1');
    $routes->post('users/(:num)/deactivate', 'Admin\UserController::deactivate/$1');
});

// API (for check-in integration)
$routes->group('api', function ($routes) {
    $routes->get('registrations/(:segment)', 'Api\RegistrationApiController::show/$1');
    $routes->post('registrations/(:segment)/checkin', 'Api\RegistrationApiController::checkin/$1');
    $routes->get('events/(:num)/stats', 'Api\EventApiController::stats/$1');
});
