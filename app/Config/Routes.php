<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Tambahkan routing untuk CRUD siswa
$routes->get('/siswa', 'Siswa::index');
$routes->get('/siswa/create', 'Siswa::create');
$routes->post('/siswa/store', 'Siswa::store');
$routes->get('/siswa/edit/(:num)', 'Siswa::edit/$1');
$routes->post('/siswa/update/(:num)', 'Siswa::update/$1');
$routes->get('/siswa/delete/(:num)', 'Siswa::delete/$1');
$routes->get('/login', 'Auth::login');
$routes->post('/login/process', 'Auth::process');
$routes->get('/profile', 'Profile::index');
$routes->get('/attendance', 'Attendance::index');
$routes->get('/loading', 'Home::loading');
