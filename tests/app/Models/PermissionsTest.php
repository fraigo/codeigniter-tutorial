<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class PermissionsTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\Permissions';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $this->insertOk = [
            'user_type_id' => 1,
            'module' => 'extra',
            'access' => 1
        ];
        $this->insertError = [
            'user_type_id' => 1,
            'module' => 'extra',
        ];
    }
    
}