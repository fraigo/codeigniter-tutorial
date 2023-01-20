<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(static function () {
    $response = \Config\Services::response();
    $response->setStatusCode(404);
    return view('default',[
        'content'=> view('errors/html/error_404',['title'=>'Not Found'])
    ]);
});
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/login', 'Auth::form');
$routes->get('/auth/recover', 'Auth::recover');
$routes->post('/auth/recover', 'Auth::doRecover');
$routes->get('/auth/reset/(:any)', 'Auth::reset/$1');
$routes->post('/auth/reset/(:any)', 'Auth::doReset/$1');
$routes->get('/auth/logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/auth/profile', 'Auth::profile');
    $routes->post('/auth/profile', 'Auth::updateProfile');
});

$routes->group('', ['filter' => 'auth:access,users,1'], static function ($routes) {
    $routes->get('/users', 'Users::index');
    $routes->get('/users/view/(:num)', 'Users::view/$1');
});
$routes->group('', ['filter' => 'auth:access,users,2'], static function ($routes) {
    $routes->get('/users/edit/(:num)', 'Users::edit/$1');
    $routes->post('/users/edit/(:num)', 'Users::update/$1');
});
$routes->group('', ['filter' => 'auth:access,users,3'], static function ($routes) {
    $routes->get('/users/new', 'Users::new');
    $routes->post('/users/new', 'Users::create/$1');
});
$routes->group('', ['filter' => 'auth:access,users,4'], static function ($routes) {
    $routes->get('/users/delete/(:num)', 'Users::delete/$1');
});
$routes->group('', ['filter' => 'auth:access,usertypes,1'], static function ($routes) {
    $routes->get('/usertypes', 'UserTypes::index');
    $routes->get('/usertypes/view/(:num)', 'UserTypes::view/$1');
});
$routes->group('', ['filter' => 'auth:access,usertypes,2'], static function ($routes) {
    $routes->get('/usertypes/edit/(:num)', 'UserTypes::edit/$1');
    $routes->post('/usertypes/edit/(:num)', 'UserTypes::update/$1');
});
$routes->group('', ['filter' => 'auth:access,usertypes,3'], static function ($routes) {
    $routes->get('/usertypes/new', 'UserTypes::new');
    $routes->post('/usertypes/new', 'UserTypes::create/$1');
});
$routes->group('', ['filter' => 'auth:access,usertypes,4'], static function ($routes) {
    $routes->get('/usertypes/delete/(:num)', 'UserTypes::delete/$1');
});
$routes->group('', ['filter' => 'auth:access,permissions,1'], static function ($routes) {
    $routes->get('/permissions', 'Permissions::index');
    $routes->get('/permissions/view/(:num)', 'Permissions::view/$1');
});
$routes->group('', ['filter' => 'auth:access,permissions,2'], static function ($routes) {
    $routes->get('/permissions/edit/(:num)', 'Permissions::edit/$1');
    $routes->post('/permissions/edit/(:num)', 'Permissions::update/$1');
});
$routes->group('', ['filter' => 'auth:access,permissions,3'], static function ($routes) {
    $routes->get('/permissions/new', 'Permissions::new');
    $routes->post('/permissions/new', 'Permissions::create/$1');
});
$routes->group('', ['filter' => 'auth:access,permissions,4'], static function ($routes) {
    $routes->get('/permissions/delete/(:num)', 'Permissions::delete/$1');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
