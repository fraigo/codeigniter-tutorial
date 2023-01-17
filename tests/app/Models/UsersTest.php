<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class UsersTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\Users';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $email = $faker->email();
        $this->insertOk = [
            'name' => 'Test Example',
            'email' => $email,
            'password' => md5('Test.123'),
            'user_type' => 1,
        ];
        $this->insertError = [
            'name' => 'Test Example',
            'email' => md5('Test.123'),
            'user_type' => 1,
        ];
    }
    
}