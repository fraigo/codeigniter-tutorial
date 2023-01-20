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
            'password' => 'Test.123',
            'user_type' => 1,
        ];
        $this->insertError = [
            'name' => 'Test Example',
            'email' => 'Test.123',
            'user_type' => 1,
        ];
        $this->updateOk = [
            'password' => 'Test.12345',
        ];
        $this->updateError = [
            'name' => null,
        ];
    }

    /**
     * @depends testInsert
     */
    public function testHashPasswordInsert()
    {
        $item = $this->model->where("id",static::$insertId)->first();
        $plainPassword = $this->insertOk["password"];
        $this->assertEquals($item['password'],md5($plainPassword),'Password Hash Error');
    }

    /**
     * @depends testUpdate
     */
    public function testHashPasswordUpdate()
    {
        $item = $this->model->where("id",static::$insertId)->first();
        $plainPassword = $this->updateOk["password"];
        $this->assertEquals($item['password'],md5($plainPassword),'Password Hash Error');
    }

    
}