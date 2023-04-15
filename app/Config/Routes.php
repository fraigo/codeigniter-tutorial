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
if (strpos(@$_SERVER["REQUEST_URI"]?:'',"/api")===0){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization");
    if ($_SERVER["REQUEST_METHOD"]=="OPTIONS"){
        http_response_code(200);
        die();
    }
}

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->post('/auth/login', 'Auth::login');
$routes->post('/api/auth/login', 'Auth::login');
$routes->get('/auth/login', 'Auth::form');
$routes->get('/auth/recover', 'Auth::recover');
$routes->post('/auth/recover', 'Auth::doRecover');
$routes->post('/api/auth/recover', 'Auth::doRecover');
$routes->get('/auth/reset/(:any)', 'Auth::reset/$1');
$routes->post('/auth/reset/(:any)', 'Auth::doReset/$1');
$routes->post('/api/auth/reset/(:any)', 'Auth::doReset/$1');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/api/auth/logout', 'Auth::logout');

$routes->get('/api/google/login/auth', 'Gapi::auth/login');
$routes->get('/api/google/login/token', 'Gapi::token');

$routes->group('', ['filter' => 'auth:access,gdrive,2'], static function ($routes) {
    $routes->get('/api/google/drive/auth', 'Gapi::auth/drive');
    $routes->get('/api/google/drive/browse', 'Gapi::browse');
    $routes->get('/api/google/drive/browse/(:any)', 'Gapi::browse/$1');
    $routes->get('/api/google/drive/select/(:any)', 'Gapi::select/$1');
});

$routes->get('/page/(:any)', 'Pages::viewBySlug/$1');
$routes->get('/uploads/images/(:any)', 'ImageController::imageUploads/$1');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/profile', 'Auth::profile');
    $routes->get('/profile/edit', 'Auth::editProfile');
    $routes->post('/profile/edit', 'Auth::updateProfile');
    $routes->get('/api/profile', 'Auth::profile');
    $routes->put('/api/profile', 'Auth::updateProfile');

    $routes->get('/search/select/(:any)', 'Search::select/$1');
    $routes->get('/api/app/notifications', 'UserNotifications::userNotifications');
    $routes->put('/api/app/usernotifications/(:any)', 'UserNotifications::updateUserNotification/$1');
    $routes->get('/api/app/lists', 'ListOptions::all');
});

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/import', 'Import::index');
    $routes->get('/import/(:any)', 'Import::index/$1');
    $routes->post('/import/(:any)', 'Import::import/$1');
});

helper('module');
$appRoutes = module_routes();

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
        $routes->put("/api/$route/(:num)", "$controller::update/$1");
    });
    $routes->group('', ["filter" => "auth:access,$route,3"], static function ($routes) use ($route, $controller) {
        $routes->get("/$route/new", "$controller::new");
        $routes->post("/$route/new", "$controller::create");
        $routes->post("/api/$route/", "$controller::create");
    });
    $routes->group('', ["filter" => "auth:access,$route,4"], static function ($routes) use ($route, $controller) {
        $routes->get("/$route/delete/(:num)", "$controller::delete/$1");
        $routes->delete("/api/$route/(:num)", "$controller::delete/$1");
    });
}

$routes->get('/_admin/auth', 'AdminConsole::auth');
$routes->get('/_admin/console', 'AdminConsole::index');
$routes->get('/_admin/schema', 'AdminConsole::schema');
$routes->get('/_admin/table/(:any)', 'AdminConsole::table/$1');
$routes->get('/_admin/emaillogs/(:any)', 'AdminConsole::emailLogs/$1');
$routes->get('/_admin/logs/(:any)', 'AdminConsole::logs/$1');
$routes->get('/_admin/(:any)', 'AdminConsole::command/$1');

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

if (is_file(APPPATH . 'Config/CustomRoutes.php')) {
    require APPPATH . 'Config/CustomRoutes.php';
}

