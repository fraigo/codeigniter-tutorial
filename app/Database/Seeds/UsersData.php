<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersData extends Seeder
{
    public function run()
    {
        // clear data
        $this->db->table('users')->truncate(); 
        $this->db->table('user_types')->truncate(); 

        // User types
        $this->db->table('user_types')->insert([
            "id" => 1,
            "name" => "Viewer",
            "access" => 1,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('user_types')->insert([
            "id" => 2,
            "name" => "Editor",
            "access" => 2,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('user_types')->insert([
            "id" => 3,
            "name" => "Creator",
            "access" => 3,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('user_types')->insert([
            "id" => 4,
            "name" => "Admin",
            "access" => 4,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('users')->insert([
            "id" => 1001,
            "name" => "Admin",
            "email" => "admin@example.com",
            "password" => md5("Admin.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 4, // admin type
        ]); 
        $this->db->table('users')->insert([
            "id" => 1002,
            "name" => "User",
            "email" => "user@example.com",
            "password" => md5("User.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 1, // regular user
        ]);

    }
}
