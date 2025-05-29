<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('', function($routes) {
    $routes->get('/', 'Weather::index');
    $routes->get('weather', 'Weather::index'); // Optional alias
    $routes->post('search', 'Weather::search');
});

$routes->get('/weather/coordinates/(:num)/(:num)', 'Weather::getByCoordinates/$1/$2');

// Test route
$routes->get('test', function() { 
    return "Test route works!"; 
});