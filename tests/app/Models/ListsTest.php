<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class ListsTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\Lists';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $item = $this->model->where('name','extra')->first();
        if ($item){
            $this->model->delete($item['id']);
        }
        $this->insertOk = [
            'name' => 'test',
            'description' => 'Description',
            'active' => 1
        ];
        $this->insertError = [
            'description' => 'Description',
        ];
        $this->updateOk = [
            'name' => 'test1',
        ];
        $this->updateError = [
            'name' => null,
        ];
    }
    
}