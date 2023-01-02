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
            "name" => "Admin",
            "email" => "admin@example.com",
            "password" => md5("Admin.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 1, // admin type
        ]); 
        $this->db->table('users')->insert([
            "id" => 1002,
            "name" => "User",
            "email" => "user@example.com",
            "password" => md5("User.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 0, // regular user
        ]);
    }
}
