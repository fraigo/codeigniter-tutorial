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
$routes->set404Override(static function ($error) {
    $request = \Config\Services::request();
    $response = \Config\Services::response();
    $response->setStatusCode(404);
    if (current_url(true)->getSegment(1)=="api"){
        $response->setJSON([
            "success"=>false,
            "errors"=>[
                "message"=>"Not found",
                "details"=>$error
            ]
        ])->send();
        die();
    }
    return view('default',[
        'content'=> view('errors/html/error_404',['title'=>'Not Found','error'=>$error])
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
$routes->post('/api/auth/login', 'Auth::login');
$routes->get('/auth/login', 'Auth::form');
$routes->get('/auth/recover', 'Auth::recover');
$routes->post('/auth/recover', 'Auth::doRecover');
$routes->get('/auth/reset/(:any)', 'Auth::reset/$1');
$routes->post('/auth/reset/(:any)', 'Auth::doReset/$1');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/api/auth/logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/profile', 'Auth::profile');
    $routes->get('/profile/edit', 'Auth::editProfile');
    $routes->post('/profile/edit', 'Auth::updateProfile');
    $routes->get('/api/profile', 'Auth::profile');
    $routes->put('/api/profile', 'Auth::updateProfile');
});

$appRoutes = [
    "users" => "Users",
    "usertypes" => "UserTypes",
    "permissions" => "Permissions",
    "lists" => "Lists",
    "listoptions" => "ListOptions",
];

foreach($appRoutes as $route => $controller){

    $routes->group('', ['filter' => "auth:access,$route,1"], static function ($routes) use ($route, $controller){
        $routes->get("/$route", "$controller::index");
        $routes->get("$route/view/(:num)", "$controller::view/$1");
        $routes->get("/api/$route", "$controller::index");
        $routes->get("/api/$route/(:num)", "$controller::view/$1");
    });
    $routes->group('', ['filter' => "auth:access,$route,2"], static function ($routes) use ($route, $controller) {
        $routes->get("/$route/edit/(:num)", "$controller::edit/$1");
        $routes->post("/$route/edit/(:num)", "$controller::update/$1");
        $routes->put("/$route/(:num)", "$controller::update/$1");
    });
    $routes->group('', ["filter" => "auth:access,$route,3"], static function ($routes) use ($route, $controller) {
        $routes->get("/$route/new", "$controller::new");
        $routes->post("/$route/new", "$controller::create");
        $routes->post("/$route/", "$controller::create");
    });
    $routes->group('', ["filter" => "auth:access,$route,4"], static function ($routes) use ($route, $controller) {
        $routes->get("/$route/delete/(:num)", "$controller::delete/$1");
        $routes->delete("/$route/(:num)", "$controller::delete/$1");
    });
}

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
