<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->group('', function($routes) {
    $routes->get('login', 'Auth::index');
    $routes->post('auth/loginProcess', 'Auth::loginProcess');
    $routes->get('logout', 'Auth::logout');
});

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'Auth::profile');
    $routes->post('profile/update', 'Auth::updateProfile');

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

    $routes->get('tindak-lanjut/download/(:num)', 'TindakLanjut::download/$1');

    $routes->group('approval', function($routes) {
        $routes->get('/', 'Approval::index');
        $routes->post('process', 'Approval::process');
    });

    $routes->group('laporan', function($routes) {
        $routes->get('/', 'Laporan::index');
        $routes->get('preview/(:num)', 'Laporan::preview/$1');
        $routes->get('exportPdf/(:num)', 'Laporan::exportPdf/$1');
        $routes->get('exportWord/(:num)', 'Laporan::exportWord/$1');
    });
});