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
