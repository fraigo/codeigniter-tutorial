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
        * Implement readonly and disabled attributes
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
* Generate Fake Test data with Faker and Models
    * Install or update Faker PHP component: `composer require --dev fakerphp/faker`
    * Create a seeder for Test data: `php spark make:seeder TestData`
    * Get an instance of a model: `$users = new \App\Models\Users()`
    * Get an instance of Faker : `$faker = \Faker\Factory::create();`
    * Use the `insert($data)` method to insert a data row: 
      `$users->insert(['name'=>'Name','email'=>'email@examplecom'])`
    * Use `$faker` methods (formatters) to generate specific types of fake data:
        * `$faker->name()` for full names
        * `$faker->email()` for emails
        * Check https://fakerphp.github.io/formatters/ for more examples of formatters
    * Make a for loop to create N rows generated with fake data
    * Check generated data with `php spark db:table {tablename}`
* Working with pagination and listings
    * In a controller, get paginated data: `$items = $model->paginate($pageSize,$pagerGroup);`
        * `$pageSize` is the number of items per page
        * `$pagerGroup` is a suffix for pager variables (in case you have multiple paginated data)
    * Use `select()` to select specific fields to display : `$items = $model->select(['name','email','updated_at'])->paginate($pageSize,$pagerGroup);`
    * Load the view including the `PagerRenderer` instance (`  'pager' => $model->pager,`)
    * In the view, call to `$pager->links($pagerGroup)` to generate automated links for pagination
        * `$pager->links()` uses the `default_full` template defined in `app/Config/Pager.php`
        * Use `$pager->simple($pagerGroup)` for a simple back/forward pagination (`default_simple` template)
    * To customize the paginator links:
        * Setup CSS styles for `ul.pagination`(full) and `ul.pager` (simple) elements 
        * Create a new paginator templates. 
            * Copy and modify from the default templates from `vendor/codeigniter4/framework/system/Pager/Views/`
            * Create `app/Views/pager/full.php` from `default_full.php`
            * Create `app/Views/pager/simple.php` from `default_simple.php`
        * Modify `app/Config/Pager.php`:
            * Set `default_full` template to `'App\Views\pager\full'`
            * Set `default_simple` template to `'App\Views\pager\simple'`
* Create a CRUD interface (Create/Read/Update/Delete)
    * A view to see/read item details (item details)
    * An empty form to create a new item
    * A form view to edit item details
    * Manage requests for update, create and delete
* Create an item detail view (Read)
    * Create a new view `users/view` (`app/Views/users/view.php`)
        * Use the same default layout of `users/index` view
        * Display each `$item` field with  a label and value.
            * Eg: `<div class="form-item"><label>Name</label><div><?=$item['name']?></div></div>`
    * Modify controller to create a `view($id)` method 
        * `$id` is the item id to be viewed
        * Generate an instance of the model: `$model = new \App\Models\Users();`
        * Get the item row by id: `$item = $model->where('id',$id);`
        * Load the view `users/view` with $item data: `return view('users/view',['item'=>$item]);`
    * Add a route to `users/view/(:num)` in `app/Config/Routes.php`
        * `$routes->get('/users/view/(:num)', 'Users::view/$1');`
            * `(:num)` will be the id of the item (eg: `/users/view/1001`)
            * `$1` will be passed to the controller method `view($id)` as a number
* Create the item edit view (Update)
    * Create a new view `users/form` (`app/Views/users/form.php`)
        * Use the same default layout of `users/index` view
        * Use the `form` helper to create a form
        * Use `form_open('/users/edit')` to setup the form 
        * Create each form field:
            * Use the field name as the input `name` attribute
            * Use `set_value('field', $item['field'])` to set the input value
        * Close the form
    * Modify controller to create a `edit($id)` method: 
        * `$id` is the item id to be edited
        * Generate an instance of the model: `$model = new \App\Models\Users();`
        * Get the item row by id: `$item = $model->where('id',$id);`
        * Load the view `users/form` with $item data: `return view('users/form',['item'=>$item]);`
    * Add a route to `users/edit/(:num)` in `app/Config/Routes.php`
        * `$routes->get('/users/edit/(:num)', 'Users::edit/$1');`
            * `(:num)` will be the id of the item (eg: `/users/edit/1001`)
            * `$1` will be passed to the controller method `edit($id)` as a number
    * Modify controller to create a `update($id)` method (update data for `$id`)
        * Generate an instance of the model: `$model = new \App\Models\Users();`
        * Get the item row by id: `$item = $model->where('id',$id);`
        * Get form data, for specific fields: `$data = $this->request->getVar(['name','email']);`
        * Get validation rules for specific fields: `$rules = $model->getValidationRules(['only'=>['name','email']]);`
        * Run validation. 
            * If validation fails, load the view `users/form` with `errors` and `item` loaded
        * If validation passes, update model with form fields: `$model->update($item["id"],$data);`
        * Redirect to `users/view/{id}` using the same requested `$id`
    * Add a route to `users/edit/(:num)` (for POST) in `app/Config/Routes.php`
        * `$routes->post('/users/edit/(:num)', 'Users::update/$1');`
            * `(:num)` will be the id of the item (eg: `/users/edit/1001`)
            * `$1` will be passed to the controller method `update($id)` as a number
    * Modify the index view `users/index.php` to add a 'View' action column for each row:
        * Use a link to `/users/view/{id}` to view the item
* Setup the form to create a new item (Create)
    * Use the same view `users/form` (`app/Views/users/form.php`)
        * Use `form_open('/users/new')` to setup the form for a new item (if `$data['id']` is not set)
        * Add fields for Password and Repeat Password
    * Modify controller to create a `new()` method: 
        * Load the view `users/form` with empty `$item` data: `return view('users/form',['item'=>[]);`
    * Add a route to `users/new` in `app/Config/Routes.php`
        * `$routes->get('/users/new', 'Users::new');`
    * Modify controller to create a `create()` method (POST new item data)
        * Generate an instance of the model: `$model = new \App\Models\Users();`
        * Get form data: `$data = $this->request->getVar(['name','email','password','repeat_password']);`
        * Get validation rules for specific fields: `$rules = $model->getValidationRules(['only'=>['name','email','password']]);`
        * Add a rule for `repeat_passsword` to match password field: `$rules['repeat_password'] = 'matches[password]';`
        * Run validation. 
            * If validation fails, load the view `users/form` with `errors`
        * If validation passes, insert model with form data: `$id = $model->insert($data);`
        * Redirect to `users/view/{id}` using the `$id` of the model inserted
    * Add a route to `users/edit/(:num)` (for POST) in `app/Config/Routes.php`
        * `$routes->post('/users/edit/(:num)', 'Users::update/$1');`
            * `(:num)` will be the id of the item (eg: `/users/edit/1001`)
            * `$1` will be passed to the controller method `update($id)` as a number
* Setup delete item requests
    * Modify controller to create a `delete($id)` method (delete item by id)
        * Get an instance of the model: `$model = new \App\Models\Users();`
        * Call to delete with `$id`: `$model->delete($id)`
        * Redirect back to the previous page (users list): `return redirect()->back();`
    * Add a route to `users/delete/(:num)` in `app/Config/Routes.php`
        * `$routes->get('/users/delete/(:num)', 'Users::delete/$1');`
    * Modify the index view `users/index` to add a 'Delete' action column for each row:
        * Use a link to `/users/delete/{id}` to delete the item
        * Include a confirmation action to send the delete request. Eg: `onclick="return confirm('Are you sure?')"`
* Generalize controller elements: Setup a base model
    * Modify the Base controller (`app/Controllers/BaseController.php`)
        * Setup a base model name property: `protected $modelName = '';`;
        * Setup a base model property: `protected $model = null;`;
        * During `initController()` setup the model: 
            * `$modelName = $this->modelName;`
            * `$this->model = new $modelName();`
    * Modify the controller (eg: `app/Models/Users.php`)
        * Setup `$modelName` property: `protected $modelName = 'App\Models\Users';`
        * Replace `$model` variables (created with `new App\Models\Users()`) with `$this->model`
* Base Controller updates: Get model data or Not found
    * Add method `getModelById($id)` to Base controller (`app/Controllers/BaseController.php`)
        * Get the item by id with `find()`: `$item = $this->model->find($id);`
        * If no `$item` is found:
            * Trigger `PageNotFoundException` exception: `throw new \CodeIgniter\Exceptions\PageNotFoundException();`
    * Replace calls to `$this->model->find($id)` or `$this->model->where('id',$id)->first()` with `$this->getModelById($id)`
    * Setup `set404Override([function])` in `app/Config/Routes.php`
        * Set status code to 404 (not found): `$response->setStatusCode(404);`
        * Return the view `errors/html/error_404`
    * Modify view `app/Views/errors/html/error_404.php`:
        * Use the default layout calling `$this->extend('layouts/default')`
        * Setup a 'Not found' message in content
* Setup Bootstrap 4
    * Use the starter template as default template:
        * https://getbootstrap.com/docs/4.0/getting-started/introduction/#starter-template
    * Use an example from https://getbootstrap.com/docs/4.0/examples/ to setup the body content 
    * Change external urls to local resources
    * Modify views to use bootstrap classes (forms)
* Sorting data with URL parameters
    * Modify controller to read a sort query parameter (eg: 'sort_users') from URL
        * `$sortQuery = 'sort_users';`
        * Read parameter with `$sort = $this->request->getVar($sortQuery);`
        * `$sort` should be a field name. It could be empty.
        * Setup model query, sort, then paginate:
            * `$query = $this->model->select(...);`
            * If `$sort` is set, apply sort: `if ($sort) $query = $query->orderBy($sort);`
            * `$items = $query->paginate($pageSize,$pagerGroup);`
        * Use the URL library to get the base URL without sort query:
            * `$sortUrl = current_url(true);`
            * `$sortUrl->stripQuery($sortQuery);`
        * Modify table column headers to create sort links
            * Use `anchor(link,title)` function
            * Link to `$sortUrl->addQuery($sortQuery,'fieldname')->toString()`
            * Title is the column name plus a sort indicator (↓) if `$sort` is active for that column
        * Add support for reverse order (DESC):
            * Link to `$sortUrl->addQuery($sortQuery,$sort=='fieldname'?'fieldname desc':'fieldname')->toString()`
            * Modify titles to show sort indicators (↓ or ↑) for regular or reverse order (desc). Eg. `"Name".($sort=='name'?' ↓':($sort=='name desc'?' ↑':'')`
* Filtering data in models (form filters)
    * In the controller, setup fields to be filtered and apply filters
        * Define filter query names structure (eg: `{$table}_{$field}`)
        * Capture filter values from URL parameters: `$value = $this->request->getVar($filterQuery)`
        * Apply filter to model query:
            * For exact matches use `$query->where($field,$value)` 
            * Por partial matches (substring) use `$query->like($field,$value)`
    * In the view, show a form with current filter values
        * Create a form with target to the current url and method GET
        * Create form controls for each filter, displaying the current value
        * Hide additional query parameters as hidden inputs (sort order, items per page)
* Remove layout configuration from views, render layout from controller
    * In views, remove calls to `$this->extend('layouts/default')`, `$this->section()`, `$this->endSection()`
    * Create a default view (`app/Views/default.php`)
        * Use default layout calling to `$this->extend('layouts/default')`
        * `$this->section()` will contain only a `$content` variable
    * In controller, render view with default view calling `view('default',['content'=>view('view/name', $data)])`
* Creating views using the Parser Class
    * It allows to create views with simple substitutions instead of PHP code.
    * PHP code is not interpreted in parser views
    * To load the system parser: `$parser = \Config\Services::parser();`
    * To render a view with $data: `$parser->setData($data)->render('view/name');`
    * To render the view inside the default view layout:  `view('default',[ 'content' => $parser->setData($data)->render('view/name') ]`
* Generalize use of view layout:
    * Modify `app/Controllers/BaseController` to add a `layout($view, $data)` method
        * Calls to `view('default',['content'=>view($view, $data)])`
    * Create a similar method `parserLayout($view,$data)` but calling to `$parser->setData($data)->render($view)`
    * Modify controller to call `$this->layout('view/name', $data)` instead of `view('view/name', $data)`
* Setup and generalize Base controller methods
    * Setup a field list
    * Setup fields to be selected
    * Apply sorting 
    * Setup reading filter values and apply model conditions
    * Setup table columns and sort headers
* Implement user types/profiles
    * Create a new migration for `user_types`
        * Each user type has its own name and access
    * Modify `users` migration 
        * Field `user_type` to reference `user_types.id`
    * Recreate the schema running `php spark migrate:refresh`
    * Modify `Auth` controller 
        * in login method, set `admin` session if user has a "full access" user type
    * Modify Users and Test data seed to setup user types accordingly
* Implement common CRUD from Base Controller
    * Generalize `index`, `edit`, `new` and `delete`
    * Modify Users controller to use BaseController methods
    * Setup field config for use with crud operations
* Setup CRUD for user types
    * Setup UserTypes controller using the ser controller as template
    * Add  CRUD routes for `usertypes` (`index`, `edit`, `new`, `delete`)
    * Add to default template menu a link to User Types `/usertypes/`
    * Setup Auth controller
        * Save the user type (profile) in a session variable
        * Setup admin session variable if user type access is full
* Implement profile access levels
    * Setup `Auth` filter to implement a filter by access
        * Filter format is `auth:access,route,level` 
        * `level` can be: read,edit,create,full(delete) (1-4)
        * `route` is the module to access. This will be implemented later.
        * Read user type access level
        * If level is lower than required, return status 403 (not authorized) 
    * Modify `app/Config/Routes.php` to check access levels
        * Use filter `auth:access,{route},{level}`
        * Separate routes by route name and access levels
        * Eg: `auth:access,users,1` for routes reading users (`users/index`,`users/view`)
    * Modify views to hide links to higher level routes
* Generalize view and edit/create forms
    * Setup field configuration 
    * Select specific fields for view/edit/create views
    * Create base views:
        * View item (`app/Vies/view.php`) 
        * Edit/create item (`app/Views/form.php`) 
    * Change view to base views in Controllers:
        * Change `users/view` and `usertypes/view` to `view` 
        * Change `users/form` and `usertypes/form` to `form`
        * Remove unused views 
    * Generalize methods for `index`, `edit`, `new` and `delete`
        * Create default methods in BaseController and remove custom ones in each Controller.
* Setup module permissions model and controller
    * Create new migration for `permissions`:
        * assing modules, access level for each user type (profile)
    * Update UserData seeder to add Permissions for each user type 
    * Create new Permissions model
    * Create Permissions controller
    * Create routes with `auth:access` filter for all operations
    * Add Permissions to the main menu
* Setup Auth login to retrieve permissions
    * Read permissions from Permissions model
    * Store `permissions` session variable
* Create `auth` helper
    * Check if user has full access `is_admin()`
    * Get profile access to modules: `profile_access($module)`
    * Check if user has access to a module: `module_access($module, $access)`
* Modify `Auth` filter to check access:
    * Implement auth filter for `admin` with `is_admin()` 
    * Implement `permissions` auth filter with `module_access()`
* Implement permissions in Controllers and views
    * Use `auth` helper to check module access
        * Edit actions in table view and item detail view
        * Create action in table view
        * View action in main menu links
    * Enforce maximum access level for user editors
* Implement and setup Unit Testing
    * Install `phpunit`: `composer require --dev phpunit/phpunit`
    * Copy and rename `phpunit.xml.dist` to `phpunit.xml`
    * Create test cases in `tests/app`
    * Create a generic test `tests/_support/TestModel`: list, get, insert, update, delete
    * Create a model test extending from `\Tests\Support\TestModel`
        * Create model tests in `tests/app/Models`
        * Filename ending with Test (eg: `UsersTest.php`)
        * Setup properties on `setup()`
* Setup session flash variables
    * Useful to send data to the next request, read and clear data
    * Save data using `session()->setFlashData('variable','content')`
    * Retrieve data in the next request using `session()->getFlashData('variable')`
* Setting and reading cookies
    * Set a cookie to remember the user email login
    * Add a checkbox in Auth controller login `form` view with 'Remember me' label
    * In controller, set cookie with `$this->response->setCookie('name','value'[,expirationInSeconds])`
    * In view, load `cookie` helper and get cookie value using `get_cookie('name')`
* Setup Email 
    * Modify `app/Config/Email.php` to set up email parameters:
        * `$protocol`: Send email via `mail` command, `sendmail` command or via `smtp` server
        * `$SMTPHost`, `$SMTPPort`, `$SMTPUser`, `SSMTPPass`: To configure `smtp` protocol
        * `$SMTPCrypto` : To select which secure protocol use for `smtp`: `tls` or `ssl`
        * `$mailType`: `html` to send HTML-formatted emails, `text` for plain text emails (no format).
    * Create email helper function
        * Get email instance `$email = \Config\Services::email();`
        * Setup sender `$email->setFrom('admin@examle.com', 'Admin');` (generally the same domain of the server)
        * Set recipient (`$email->setTo($to)`) and subject(`$email->setSubject($subject)`)
        * Setup the message body from a view `$email->setMessage(view($view,$data))` or directly by a `string` value
        * Send message using `send()` method: `$email->send()`
* Setup Auth controller to support password recovery
    * Modify or add migration to create a new field `password_token` in `users` table
    * Run migration to create the new field
    * Update `app/Models/Users` model to include the new field and validations if needed
    * Create a view for password recovery `app/Views/auth/recovery.php`
        * Setup an `email` field to request password recovery
        * Form action to `/auth/recovery` (POST)
    * Create a view to generate the email recovery message (`app/Views/email/recovery.php`)
        * Include the user `$name` and the `$link` for account recovery as parameters.
        * You can use HTML if `$mailType` is `'html'` in `app/Config/Email.php`
    * Update `Auth` controller to add a `recover()` method:
        * Render view `auth/recovery`
    * Modify `Auth` controller to create a `doRecover()` method:
        * Read `email` value from recovery form
        * Validate email. In any error, return the form again with errors `$this->recover()`
        * If valid, search in user model for the email address: `$user = $this->model->where('email',$email)->first()`
        * If a user is found, send a recovery email using the `email` helper:
            * Recipient will be `$user['email']` 
            * Subject is `Password Recovery`
            * View is set to `email/recovery`
            * Generate a password `$token` and update `password_token` field with this value in the model
            * Include parameters like `name` and `url` for the view
        * If `send_email()` fails (returns `false`) return to the form with error
        * No matter whether a user is found or not, a success message will be set
    * Modify `app/Config/Routes.php` to include :
        * Recovery form view `$routes->get('/auth/recover', 'Auth::recover');`
        * Recovery processing: `$routes->post('/auth/recover', 'Auth::doRecover');`
    * Modify login form view to include a link to Recover password `/auth/recover`
    * Create a form view to reset the password `app/Views/auth/reset.php`
        * If no `$user` is found, display message 'Token is invalid or has expired'
        * If the `$user` exists, display form with `new_password` and `repeat_passsword`
        * If `$success`, display a success message 'Your password was successfully changed`
    * Add a method `reset($token)` to the `Auth` controller:
        * Find a `$user` where `$token` matches `password_token`
        * Render the view `auth/reset` with `$token` and `$user` data
    * Add method `doReset($token)` to process the new password
        * Verify if `$token` is still valid
        * Vefify `new_password` is valid and `repeat_password` matches
        * If password passes verification, update user model:
            * Set `password` 
            * Clear `password_token`
        * If any error ocurrs, display the reset form again with errors.
    * Add routes for `reset()` and `doReset()` methods in `Auth` controller:
        * Reset form view `$routes->get('/auth/reset/(:any)', 'Auth::reset/$1');`
        * Reset password processing: `$routes->post('/auth/reset/(:any)', 'Auth::doReset');`
* Prepare model data before insert/update
    * Create method `hashPassword($data)` in `User` model to implement password hashing
    * Add event callbacks for insert/update events in `User` model:
        * Set `beforeInsert` and `beforeUpdate` to `['hashPassword']`
    * Remove references to password hashing in controllers
* Implement profile update (current user)
    * Add to `Auth` controller methods `profile()` and `updateProfile()`
    * Each one calls an instance of User Controller
        * Init User controller
        * Get user id from `auth` session variable (`session('auth')['id]`)
        * Call to `Users::profile($id)` or `Users::updateProfile($id)`
    * Implement  `profile($id)` and `updateProfile($id)` in User controller
        * Setup entity name to 'My Profile'
        * Call to methods `edit($id)` and `update($id)` in each one
    * Add routes for `auth/profile` (GET) and `auth/profile` (POST) 
        * Setup inside filter `auth` (logged in user)
    * Add 'My Profile' menu to default view (when `auth` session is active)
* Implement a RESTful controller
    * Change BaseController to extend from `CodeIgniter\RESTful\ResourceController`
        * Modify `edit($id)`, `update($id)` and `delete($id)` methods to use a default `($id=null)` (compatible with `BaseResourceController`)
        * Create method to return detect if the request is JSON `isJSON()`
            * `return strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;`
        * Create a method to return JSON data instead of views `JSONResonse($data,$status,$errors)`
            * `return $this->response->setStatusCode($status)->setJSON($response);`
        * Extend the `index()` method to return JSON results for REST API requests
            * When pagination is used, set the pager group to `'default'`
            * Also, return the current `$page` number, number of `$pages` and `$next` page URL:
                * `$page = $this->model->pager->getCurrentPage('default');`
                * `$pages = $this->model->pager->getPageCount('default');`
                * `$next = current_url(true)->addQuery("page",$page+1);`
        * Change `getModelById($id)` to return a JSON error if no item is found for API requests
        * Modify `update($id)` and `create($id)` methods to read JSON data and return a JSON object for the item requested
        * Change `delete($id)` method to return a JSON response after a delete REST api request
    * Change other controllers (`Users`) to accomodate changes in BaseController
    * Add routes for REST API commands. Eg: for `users` 
        * Use the same access permissions of the regular request actions
        * For index action, add `api/users` (list items) and `api/users/ID` for single items
        * In the edit actions, add `api/users/ID` (PUT) and for create add `api/users` (POST)
        * For delete, add `api/users/ID` (DELETE method)
* Implement authentication responses for REST API requests
    * Implement a JSON error response when authentication or access is rejected in `Auth` filter
        * Detect if the request is a JSON api requesst (`isJson($request)`)
        * Return a JSON response with status code 403: `$response->setStatusCode(403)->setJSON([])`
    * Implement a JSON response for `Auth` controller methods (login/logout)
* Implement JSON responses for profile view and edit
    * In `User` controller , detect if the request is a JSON api requesst
    * Return JSON responses for successful or failed results
* Debug email messages in Local SMTP Server
    * Install NodeMailer App from https://nodemailer.com/app/
    * Run the application
    * Create a new project, use the default name
    * Go to menu Server -> Start Server
    * Go to the left panel "Local Server"
    * Use the configuration parameters there for your Email config (`app/Config/Email.php`)
        * `$protocol = 'smtp'`
        * `$SMTPHost = 'localhost'`
        * `$SMTPUser = 'project.1'`
        * `$SMTPPass = 'secret.1'`
        * `$SMTPCrypto = ''`
    * Test the email server going to `/auth/recover`
* Setup password reset token expiration
    * Create a migration to add a new field `password_token_expires` in `users` table
    * Update User model to include `password_token_expires` field
    * Edit `Auth` controller `doRecover()` method to set `password_token_expires` to a future date (eg: +6 hours)
        * `$user["password_token_expires"] = Time::parse("+6 hours")->toDateTimeString()`
    * Modify `Auth` controller `reset()` and `doReset()` to check if `password_token_expires` is after the current date/time (not expired)
* Create authentication token
    * Create a migration to add a new field `auth_token` in `users` table
    * Update User model to include `auth_token` field
    * Edit `Auth` controller `login()` method to set `auth_token` when no token is set
* Setup 404 error for JSON/Api requests
    * Detect json request and send JSON error.
    * Update 404 error view to show the request error.
    * Setup API endpoints for login/logout: `/api/auth/login`, `api/auth/logout`
* Setup authentication by token + global login helper
    * Modify `auth` helper to include:
        * `create_token()` function to generate new tokens
        * `do_login($id)` to process login info for by user ID
            * Saves `user` and `profile` data
        * `clear_login()` to remove `user` and `profile` data
        * `check_login()` to perform authentication token check and do login
        * `logged_in()` to check if user data is present
        * `current_user()` to return logged in user + profile data
        * `user_id()` to return the current user ID
    * Modify existing `auth` helper functions to accomodate session variables
    * Modify `Auth` controller to use `do_login($id)` to process login data
    * Modify `Auth` filter 
        * Use `auth` function to check login by token
    * Setup a common reject method to return when access is forbidden or invalid.
    * Modify views to use new `auth` helper methods to check login info
* Setup common routes for Controllers
    * Create an array of route => Controller pairs
    * Setup common routes for create, read, update and delete items
* Setup and override a custom service
    * Create a custom library in `app/Libraries` (eg: `app/Libraries/EmailLogger.php`)
    * Create a new entry in `app/Config/Services.php` to override the `email` method:
        * `public static function email($config = null, $getShared = true){ ... }`
        * When `$getShared` is `true`, return `self::getSharedInstance('email', $config);`
        * Else, return a new instance of the Library class (eg: `return new \App\Libraries\EmailLogger($config)`)
* Implement a Custom logger:
    * Call to the PHP function `error_log($message, $message_type, $destination)`
    * For `$message_type` use `3` to append to a file (or create the file if it doesn't exist)
    * Use `$destination` to define the filename to use to append the log:
        * Path is WRITABLE + '/logs'
        * File name is `email-{DATE}.log`
    * Add a new line character `"\n"` to `$message` to geerate new lines in each log entry.
* Implement development-only routes
    * Create a route configuration file only for development:
        * Filename is `app/Config/development/Routes.php`
        * Add routes only for development. 
        * Example: Logger viewer `$router->get('logs/default',function(){ ...read logs... })`
* Setup a custom component - Show/coyp token
    * Create a view (eg: `app/Views/components/password-view.php`) to setup the component contents using $config parameters
    * Call the view with `view('components/password-view.php'` and passing configuration values
    * Setup control name in form_helper, create function name `function password_view($config){ }` calling the component view
* Upgrade component to use alpine.js data bindings
    * Import alpine.js file in default layout: `<script src="//unpkg.com/alpinejs" defer></script>`
    * Setup component to use attributes like `x-init`, `x-data`, `x-on`, `x-bind` 
    * Set content with `x-text`, `x-html`
    * Assign input values with `x-model`
    * Setup visibility with `x-show` and `x-transition`
    * Build loops with `x-for` and conditionals with `x-if`
    * Assign element references with `x-ref` and use them with `$refs.refname`
* Setup a module helper to get available modules and routes
    * Create new module helper `app\Helpers\module_helper()`
        * Create `module_list()` function with routes and module names
    * Load module helper in `BaseController` `$helpers` attribute
    * Load module helper in default view to create access menus from `module_list()`
    * Call `module_list()` in Controllers or Views listing module names
* Improve details section to edit related items
    * Setup BaseController `editLink` and `newLink` attributes to allow overwrite default links in table view
    * Modify `new` and `edit` methods to allow set and fix related fields
    * Setup Controller `getDetails()` to overwrite view/new/edit links
* Implement system lists
    * Create migration for `lists` table (list header): `php spark make:migration CreateLists`
        * Setup a list `name` (and description)
    * Create migration for `list_options` table (list details): `php spark make:migration CreateListOptions`
        * Setup `list_id`, item `name` and `value` fields
    * Run migrations: `php spark migrate`
    * Create models for Lists and ListOptions
    * Setup controllers for Lists and ListOptions
    * Setup `getDetails()` in Lists controller using `ListOptions` controller
    * Add new modules to module helper `module_list()` function
    * Add CRUD routes for `lists` and `listoptions`
    * Creaet Unit tests for both models

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
