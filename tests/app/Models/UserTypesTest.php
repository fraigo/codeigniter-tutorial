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
        $item = $this->model->where('name','UserType')->first();
        if ($item){
            $this->model->delete($item['id']);
        }
        $this->insertOk = [
            'name' => 'UserType',
            'access' => 1
        ];
        $this->insertError = [
            'name' => 'UserType',
            'access' => null
        ];
        $this->updateOk = [
            'name' => 'UserTypeUpdated',
        ];
        $this->updateError = [
            'name' => null,
        ];
    }
    
}