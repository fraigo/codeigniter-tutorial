<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class ListOptionsTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\ListOptions';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $item = $this->model->where('name','extra')->first();
        if ($item){
            $this->model->delete($item['id']);
        }
        $this->insertOk = [
            'list_id' => 1,
            'name' => 'extra',
            'value' => 'Extra',
        ];
        $this->insertError = [
            'value' => 'Extra2',
        ];
        $this->updateOk = [
            'name' => 'extra1',
            'value' => 'Extra1',
        ];
        $this->updateError = [
            'name' => null,
        ];
    }
    
}