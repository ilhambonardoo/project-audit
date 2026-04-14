<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

