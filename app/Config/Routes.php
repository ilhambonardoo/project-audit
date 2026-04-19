<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');

$routes->group('temuan', function($routes) {
    $routes->get('/', 'Temuan::index');
    $routes->get('show/(:num)', 'Temuan::show/$1');
    
    $routes->group('', ['filter' => 'role_id:1'], function($routes) {
        $routes->get('create', 'Temuan::create');
        $routes->post('store', 'Temuan::store');
        $routes->get('edit/(:num)', 'Temuan::edit/$1');
        $routes->post('update/(:num)', 'Temuan::update/$1');
        $routes->get('delete/(:num)', 'Temuan::delete/$1');
        $routes->post('verify', 'Temuan::verify');
    });
});

$routes->group('tindak-lanjut', ['filter' => 'role_id:2'], function($routes) {
    $routes->get('create/(:num)', 'TindakLanjut::create/$1');
    $routes->post('store', 'TindakLanjut::store');
});

$routes->group('approval', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Approval::index');
    $routes->post('process', 'Approval::process');
});