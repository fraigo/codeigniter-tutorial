<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersData extends Seeder
{
    public function run()
    {
        // clear data
        $this->db->table('users')->truncate(); 
        $this->db->table('users')->insert([
            "id" => 1001,
            "email" => "admin@example.com",
            "password" => md5("Admin.123"),
            "user_type" => 1, // admin type
        ]); 
        $this->db->table('users')->insert([
            "id" => 1002,
            "email" => "user@example.com",
            "password" => md5("User.123"),
            "user_type" => 0, // regular user
        ]); 
    }
}
