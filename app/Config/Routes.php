<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('barang', 'Barang::index');
$routes->get('barang/tambah', 'Barang::tambah');
$routes->get('barang/edit/(:any)', 'Barang::edit/$1');
$routes->post('barang/add', 'Barang::add');
$routes->post('barang/update', 'Barang::update');
$routes->get('barang/hapus/(:any)', 'Barang::hapus/$1');
$routes->get('barang/gambar/(:segment)', 'Barang::gambar/$1');
