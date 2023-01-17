<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class UserTypesTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\UserTypes';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $this->insertOk = [
            'name' => 'UserType',
            'access' => 1
        ];
        $this->insertError = [
            'name' => 'UserType',
            'access' => null
        ];
    }
    
}