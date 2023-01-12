<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestData extends Seeder
{
    public function run()
    {
        $users = new \App\Models\Users();
        $faker = \Faker\Factory::create();
        for($i=0; $i<100; $i++){
            $result=$users->insert([
                "name" => $faker->name(),
                "email" => $faker->email(),
                "password" => md5("User.123"),
                "user_type" => 2,
            ]);
        }
    }
}
