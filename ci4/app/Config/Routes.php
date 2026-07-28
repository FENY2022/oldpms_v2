<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Home::index');
$routes->post('/login', 'Auth\Login::index');
$routes->get('/register', 'Auth\Register::index');
$routes->post('/register', 'Auth\Register::index');
$routes->get('/verify', 'Auth\Verify::index');
$routes->get('/forgot-password', 'Auth\ForgotPassword::index');
$routes->post('/forgot-password', 'Auth\ForgotPassword::index');
$routes->get('/reset-password', 'Auth\ResetPassword::index');
$routes->post('/reset-password', 'Auth\ResetPassword::index');
$routes->get('/logout', 'Auth\Logout::index');
$routes->post('/contact', 'Api\Contact::submit');

// AJAX API Routes
$routes->post('/api/address/municipalities', 'Api\Address::getMunicipalities');
$routes->post('/api/address/barangays', 'Api\Address::getBarangays');
$routes->post('/api/application/update-file', 'Api\Application::updateFileStatus');
$routes->post('/api/requirements/reorder', 'Api\Requirements::reorder');

// Client Routes (auth required)
$routes->group('client', ['filter' => 'auth:client'], function ($routes) {
    $routes->get('/', 'Client\Dashboard::index');
    $routes->get('dashboard', 'Client\Dashboard::index');
    $routes->get('applications', 'Client\MyApplications::index');
    $routes->post('applications', 'Client\MyApplications::index');
    $routes->get('create-application', 'Client\CreateApplication::index');
    $routes->post('create-application', 'Client\CreateApplication::index');
    $routes->get('update-application/(:num)', 'Client\UpdateApplication::index/$1');
    $routes->post('update-application/(:num)', 'Client\UpdateApplication::index/$1');
    $routes->get('profile', 'Client\Profile::index');
    $routes->post('profile', 'Client\Profile::index');
});

// Admin Routes (denr_user auth required)
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('applications', 'Admin\Applications::index');
    $routes->get('view-application/(:num)', 'Admin\ViewApplication::index/$1');
    $routes->post('view-application/(:num)', 'Admin\ViewApplication::index/$1');
    $routes->get('manage-users', 'Admin\ManageUsers::index');
    $routes->post('manage-users', 'Admin\ManageUsers::index');
    $routes->get('manage-requirements', 'Admin\ManageRequirements::index');
    $routes->post('manage-requirements', 'Admin\ManageRequirements::index');
    $routes->get('manage-requirements/delete/(:num)', 'Admin\ManageRequirements::delete/$1');
});
