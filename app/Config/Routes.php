<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('temuan', function($routes) {
    $routes->get('/', 'Temuan::index');
    $routes->get('create', 'Temuan::create');
    $routes->post('store', 'Temuan::store');
    $routes->get('show/(:num)', 'Temuan::show/$1');
    $routes->post('verify', 'Temuan::verify');
    $routes->get('edit/(:num)', 'Temuan::edit/$1');
    $routes->post('update/(:num)', 'Temuan::update/$1');
    $routes->get('delete/(:num)', 'Temuan::delete/$1');
    $routes->get('/dashboard', 'Dashboard::index');
});

$routes->get('/login', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');
