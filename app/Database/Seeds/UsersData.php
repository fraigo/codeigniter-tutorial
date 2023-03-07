<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersData extends Seeder
{
    public function run()
    {
        // clear data
        echo "Cleanup Users\n";
        $this->db->table('users')->truncate(); 
        echo "Cleanup User Permissions\n";
        $this->db->table('permissions')->truncate();
        echo "Cleanup User Profile types\n";
        $this->db->table('user_types')->truncate();
        echo "Cleanup User Options\n";
        $this->db->table('user_options')->truncate();
        
        // User types
        echo "Add Profile Types\n";
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

        echo "Add Permissions\n";
        $this->db->table('permissions')->insert([
            "id" => 1,
            "user_type_id" => 1,
            "module" => "users",
            "access" => 1,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('permissions')->insert([
            "id" => 2,
            "user_type_id" => 2,
            "module" => "users",
            "access" => 2,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('permissions')->insert([
            "id" => 3,
            "user_type_id" => 3,
            "module" => "users",
            "access" => 3,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        
        echo "Add Users\n";
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
        $this->db->table('users')->insert([
            "id" => 1003,
            "name" => "Editor",
            "email" => "editor@example.com",
            "password" => md5("Editor.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 2, // regular user
        ]);
        $this->db->table('users')->insert([
            "id" => 1004,
            "name" => "Creator",
            "email" => "creator@example.com",
            "password" => md5("Creator.123"),
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "user_type" => 3, // regular user
        ]);
        
        echo "Add User Options\n";
        $userOptions = new \App\Models\UserOptions();
        $userOptions->createUserOptions(1001);
        $userOptions->createUserOptions(1002);
        $userOptions->createUserOptions(1003);
        $userOptions->createUserOptions(1004);
    }
}
