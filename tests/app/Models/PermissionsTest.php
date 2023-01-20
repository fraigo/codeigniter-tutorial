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
        $item = $this->model->where('module','extra')->first();
        if ($item){
            $this->model->delete($item['id']);
        }
        $this->insertOk = [
            'user_type_id' => 1,
            'module' => 'extra',
            'access' => 1
        ];
        $this->insertError = [
            'user_type_id' => 1,
            'module' => 'extra',
        ];
        $this->updateOk = [
            'module' => 'extra1',
        ];
        $this->updateError = [
            'module' => null,
        ];
    }
    
}