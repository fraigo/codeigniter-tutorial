# CodeIgniter 4 Application

## Setup Tutorial

* Install prerequisites
    * PHP 7.2.5+ (https://www.php.net/manual/en/install.php)
    * Composer (https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
    * Optionally, a Database engine like MySQL or PostgreSQL
* Download Codeigniter or create a Codeigniter project
    * Follow instructions from https://codeigniter4.github.io/userguide/installation/
    * Manual Installation:
        * Download Codeigniter source code https://github.com/CodeIgniter4/framework/releases/latest
        * Note: Updates to the Codeigniter codebase should be done manually
    * Install via composer
        * Run `composer create-project codeigniter4/appstarter [foldername]`
        * To update Codeigniter codebase, run `composer update`
* Setup `.env` file
    * Copy `env` file to `.env`
    * Uncomment `CI_ENVIRONMENT = development`
    * To set up an SQLite database, uncomment and set the driver and filename:
        * `database.default.DBDriver = SQLite3`
        * `database.default.database = database.sqlite`
* Start the web service locally
    * Run `php spark serve` to start a local dev server at `http://localhost:8080`
    * Press `Ctrl+C` to stop the server
    * To use a different port run `php spark serve --port PORTNUMBER`
* Create your home page view
    * Modify `app/Views/welcome_message.php` (or create a new view file `app/Views/home.php`)
    * Include your own HTML code 
    * If you want to change the file name of the home view to app/Views/home.php
        * Modify the controller `app/Controllers/Home.php` to return `view('home')` instead of `view('welcome_message')`.
        * If you created a new file, remove the old file app/Views/welcome_message.php
    * Include any additional static files in the `public/` folder
* Create a migration file (to create `user` table)
    * Use a meaningful name for the migration
        * `php spark make:migration CreateUser`
    * Edit File `app/Database/Migrations/{TIMESTAMP}_CreateUser.php`:
        * Add fields: `$this->forge->addField(["fieldname"=>[fieldconfig]])` 
        * Add primary key: `$this->forge->addKey('fieldname', true);` 
        * Add unique key: `$this->forge->addUniqueKey(['field1',''field2']);` 
        * Create the table: `$this->forge->createTable('tablename');` 
    * For the migration `down()` method, add the opposite actions to remove/create fields
        * Drop table: `$this->forge->dropTable('TABLENAME');`
        * Drop fields: `$forge->dropColumn('table_name', ['column_1', 'column_2']);`
        * Drop index: `$forge->dropKey('tablename', 'index_name');`
* Run the migration file
    * `php spart migrate` (It will run all Migrations in order of timestamp)
    *  Table `users` will be created in the database
* Create a seeder file (table data)
    * Run `php spark make:seeder SeederName` (eg: `UserData`)
    * Edit file `app/Database/Seeds/SeederName.php` 
    * Use `$this->db->table('tablename')->trunctate();` fo clear data in a table
    * Use `$this->db->table('tablename')->insert($data);` fo insert a key => value data array
    * `$data` must contain values for at least all non-null fields
* Run seeders
    * Call `php spark db:seed SeederName`
    * Table data will be populated
    * You can see the contents of a table running `php spark db:table tablename`
* Create a Base model
    * Create a file in `app/Models/BaseModel.php`
    * Set namespace `App\Models`
    * Create the BaseModel class extending from `Codeigniter\Model`
    * Add any common methods for models here
* Create a model class
    * Run `php spark make:model ModelName` (eg: Users)
    * Edit model class `app/Models/ModelName.php`
        * `$table`: is the real name of the table in the database.
        * `$allowedFields`:  define which fields of the table could be edited by the user. Generally, we exclude the table id field and any automatic or internal fields (like creation and update timestamps)
        * `$validationRules`: a set of validations to help the controller to check whether the data to be inserted or updated follow the rules or not. Some examples of rules are: `required`, `max_length[number]`, `valid_email` and others. More info on validations is in the Codeigniter 4 documentation.
        * Change `extends Model` to `extends BaseModel` to use the common Base Model class just created.
* Create a controller
    * Run `php spark make:controller ControllerName`
    * Edit file created `app/Controllers/ControllerName.php`
        * Get a reference to the model `$model = new \App\Models\Users();`
        * Retrieve all rows from the model `$items = $model->findAll();`
        * Return a response with model data
            * Directly return content. For example, JSON data.
              `return $this->response->setJSON($items);`
            * Return a view (eg: `users/index` will map to `app/views/users/index.php`)
              `return view('users/index', ['items' => $items]);`
    * To make the controller RESTful, change inheritance to `ResourceController` 
        * Change to `use CodeIgniter\RESTful\ResourceController;`
        * Change `extends BaseController` to `extends ResourceController`
        * Setup the source $modelName
            * `protected $modelName = 'App\Models\ModelName';`
        * Setup the output format (generally, json)
            * `protected $format    = 'json';`
        * For the `index` method return `$this->respond($this->model->findAll());`
* Create routes
    * Modify the file `app/Config/Routes.php` to setup routes to controllers
    * Use `$routes->get('/path/','Controller::method');` for common GET requests
    * Use `$routes->post`, `$routes->delete`, `$routes->put` for other methods
    * To pass parameters to the controller:
        * Use parameters in the path like `(:any)` (text) and `(:num)` to enforce numbers
        * Pass parameters to the controller as `$1`, `$2`, etc
        * Example: `$routes->get('/user/(:num)','User::show/$1');`
* Create or extend helpers
    * Create or extend a helper adding a file in `app/Helpers/{helpername}_helper`
    * Some existing helpers:
        * `array`: Array processing functions like `array_sort_by_multiple_keys($array, $columns` to search, sort, and format arrays. See Array Helper in the Codeigniter 4 documentation
        * `file`: To get some file information (eg: `get_file_info()`) and perform file operations like `write_file($path, $data, $mode`.
        * `html`: HTML generation shortcut functions to generate different HTML tags like `ul`, `link`, `img` and others. 
        * `form`: A set of HTML form utilities to create a form like `form_open()` and `form_close()`. Also HTML controls like `form_input()`, `form_dropdown()`, and `form_button()`
    * Load a helper in any part of the controller or view by calling `helper('helpername')`
    * In a controller, helpers could be loaded at startup by setting the `$helpers` property:
      `protected $helpers = ['html'];`
    * Once a helper is loaded, you can call any function or class created in the helper file.
* Create a view
    * Views are php files stored in `app/Views/`. We can group views by controller to store them in the same folder (eg: `app/Views/users/index.php` in the `users` subfolder)
    * To load a view from a controller or another view use `view('path/to/view', $data)`
    * To pass data to the view use the second `view()` parameter. Any value inside `$data` will pass as a $variable in the target view.
* Setup a common layout to simplify views
    * Create a common/default page structure to reuse. Store it as a default layout (`app/Views/layouts/default.php`)
        * Setup the HTML content (head, body and common content)
        * Include common variables (eg: a page `$title`)
        * Setup section placeholders using `$this->renderSection('{section name}')` (eg: section name `'content'`)
    * Load the layout in any view:
        * Call first `$this->extend()` to load the layout (eg: `$this->extend('layouts/default')`)
        * Call `$this->section('{section name}')` at the start of the section and `$this->endSection()` at the end.
    * Add additional data for the layout in the main `view()` call (eg: for `title`)
* Create authentication controller
    * Run `php spark make:controller Auth`
    * Modify `app/Controllers/Auth.php` to set a login method
        * Read login `email` + `password` data:`$data = $this->request->getVar();`
        * Check user email + password (hash md5) on user model:
            * `$users = new \App\Models\Users();`
            * `$user = $users->where("email",$data["email"])->where("password",md5($data["password"]))->first();`
        * If `$user` does not exist, return error: `'User or password incorrect'`
        * If `$user` exists, create a session variable and redirect to `/`
    * Modify `app/Controllers/Auth.php` to set a logout method
        * remove session variables
        * return OK
    * Create routes for login and logout in `app/Confir/Routes.php`
        * `$routes->post('/auth/login','Auth::login');`
        * `$routes->get('/auth/logout','Auth::logout');`
* Work with validations
    * Create $validation instance: `$validation = \Config\Services::validation();`
    * Setup rules: 
        * `$validation->setRules(['email'=>'valid_email','password'=>'required]);`;
    * Use the long rule format for more customizations, including a label and custom error messages:
        ```php
        [
          'email' => [
            'label'  => 'User Email',
            'rules'  => 'valid_email',
            'errors' => [
                'valid_email' => '{field} must ve a valid email',
            ]
          ]
        ]
        ```
    * Run validation `$validation->run($data)`
        * If returned `false`, some validations were not passed. 
        * `$validation->getErrors()` return an array of field => validation error. 
        * Error example (`valid_email` failed): `['email'=>'The email field must contain a valid email address.']`
* Add validation to Authentication login
    * Create $validation instance: `$validation = \Config\Services::validation();`
    * Setup rules for `email` and `password`: `$validation->setRules(['email'=>'valid_email','password'=>'required]);`;
    * Run validation `$validation->run($data)`
        * If error (return `false`), return form errors: `$validation->getErrors()`
* Setup a Login form
    * Create login form view `app/Views/auth/form.php` 
        * Use the default layout `$this->extend('layouts/default')`
        * Start the form `<form action="auth/login" method="POST">`
        * Email field with label: `<div><label>Email</label><br><input type="text" name="email" value="">`
        * Passowrd field with label: `<div><label>Email</label><br><input type="password" name="password" value="">`
        * Add a submit button: `<div><input type="submit" value="Log In">`
        * Display validation $errors from controller: `<div><?= implode('<br>',$errors?:[]) ?></div>`
        * Close the form: `</form>`
    * Modify Auth controller `app/Controllers/Auth.php`
        * Create a new controller method `form()` 
            * Return the login form view: `return view('auth/form',['errors'=>null])`
        * Modify the `login() method
            * If the validation fails, now return the login form with validation errors `return view('auth/form',['errors'=>$validation->getErrors()]);`
            * if user is not found, return the login form with a custom error `return view('auth/form',['errors'=>['User or password is incorrect']]);`
        * Optionally, create class attributes for login redirect and logout redirect.
    * Modify routes `app/Config/Routes.php`
        * Add route for the logout method: `$routes->get('auth/logout');`
        * Add route for the login form: `$routes->get('/auth/login', 'Auth::form');`
* Use and extend the form helper
    * Create `app/Helpers/form_helper.php` to additional form utilities 
        * `form_item($config)` container for a label + input + error message
    * Load the form helper: `helper('form')`
    * In `content` section, change HTML form content to PHP calls:
        * Open form: `echo form_open('/auth/login');`
        * Replace HTML fields and labels for email and password. 
            * Use `form_item($config)` to create inputs + labels + error message
            * Use `form_input($config)` to create single inputs
        * Create a submit button: `echo form_submit('','Log In')`
        * Close the form: `form_close()`
* Create a Controller filter for authentication
    * Run `php spark make:filter FilterName` (eg: `php spark make:filter Auth`)
    * Edit `before()` method in file `app/Filters/Auth.php`
        * Check the session variable created at login `session('auth')`
        * If not set, return a redirect to the home (or login) page `return redirect()->to(site_url('/'));`
    * Edit the file `app/Config/App.php` to remove `index.php` from the base URL
        * `public $indexPage = '';`
    * Register the Auth filter in `app/Config/Filters.php`
        * Add `'auth' => \App\Filters\Auth::class,` to `$aliases`
    * Modify `app/Config/Routes.php` to create a filtered (authenticated-only) route group:
        * `$routes->group('', ['filter' => 'auth'], static function ($routes) { /* $restricted $routes */ })`,
        *  Include `/users` route to the restricted group `$routes->get('/users', 'Users::index');`
    * Try loading `localhost:8080/users` without login. It will redirect to home (or login) page.
* Modify layout and views to check login status
    * Modify the default layout `app/Views/layouts/default.php`
    * Check session variable `session('auth')` to show or hide menus:
        * If looged in, show Users (`/users`) and Logout (`/auth/logout`) links
        * If not logged in, show only Log in link (`/auth/login`)
    * In home page view, you can display the current user email or name (if set)
        * `echo @session('auth')['email'] `
* Rebuild schema, fix tables
    * Run `php spark migrate:rollback` to undo schema changes 
    * Edit the migration file (eg: `app/Database/Migrations/{timestamp}_CreateUser.php`). Add/remove fields, modify rules, etc
    * If applicable, modify any seeder data having schema changes (eg: `app/Database/Seeders/UserData.php`)
    * Run again `php spark migrate` to create the table(s) again with fixes.
    * Run any seeders again to populate tables (`php spark db:seed UserData`)
* Create custom validations
    * Create a new class file (eg: `app/Config/CustomValidations.php`)
    * Add this class reference to `$ruleSets` array in `app/Config/Validations`:
        * eg: `\Config\CustomValidations::class,`
    * Create validator function
        * Assign a unique name (eg: `password_strength($value, $params, $data)`)
        * `$value` is the value of the current field being validated (eg: password)
        * `$params` will contain parsed parameters of the rule (between `[]`). Eg: `rule[param1,param2,{id}]` will convert to `['param1','param2',$data['id']]`
        * `$data` is the data array being validated
        * Return `false` if the validation failed, otherwise return `true`
    * Add generic error messages to `app/Language/en/Validation.php`: 
        * `[ 'password_strength' => 'Password must contain letters and numbers' ]`
    * Use your validation rule in a controller:
        * `'password' => 'required|min_length[6]|password_strength'`
        * Values passed between `{}` are converted to field values from validation data
        * Optionally, define a label and custom message for the rule (array notation):
            * `'password' => [ `
            * `  'label' => 'Password',`
            * `  'rules' => 'required|min_length[6]|password_strength', `
            * `  'errors' => [ 'password_strength' => 'Password is not strong', `
            * `]`
* Setup filter arguments - user route permissions
    * Modify Auth login method `app/Controllers/Auth.php` to setup a user role session variable (eg: `admin` = `true` for an admin user): 
        * `$session->set('admin', $user["user_type"]==1);`
    * Modify Auth filter `app/Filters/Auth.php`
        * Check `$arguments` if it has `admin`: `if ($arguments[0]=='admin')`
        * If `admin` argument is enabled, check if `session('admin')` is enabled (logged user is admin)
        * If not admin, it could return:
            * Error 403 (Not authorized)
            * Redirect to the home or login page.
    * Modify Routes to be restricted `app/Config/Routes.php`
        * Setup router group with `['filter' => 'auth:admin']` for admin-only routes (eg: `/users`)
        * Setup group filter to only `auth` (eg: `['filter' => 'auth']`) for routes for regular logged-in users
    * Modify the default layout `app/Vies/layouts/default.php` to restrict the `Users` link to only admin users (check `auth` session variable, `session('auth`)`)

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

The user guide corresponding to the latest version of the framework can be found
[here](https://codeigniter4.github.io/userguide/).

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
