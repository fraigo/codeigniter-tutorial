<?php

namespace App\Models;

use CodeIgniter\Test\CIUnitTestCase;

class PagesTest extends \Tests\Support\TestModel
{
    protected $modelClass = 'App\Models\Pages';

    protected function setUp(): void
    {
        parent::setUp();
        $faker = \Faker\Factory::create();
        $item = $this->model->where('title','Extra')->first();
        if ($item){
            $this->model->delete($item['id']);
        }
        $this->insertOk = [
            'title' => 'Extra',
            'description' => 'Description',
            'contents' => '<h1>Content</h1>'
        ];
        $this->insertError = [
            'title' => 'Error',
        ];
        $this->updateOk = [
            'description' => 'New Desription',
        ];
        $this->updateError = [
            'title' => null,
        ];
    }
    
}