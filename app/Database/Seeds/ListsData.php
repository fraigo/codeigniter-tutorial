<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ListsData extends Seeder
{
    public function run()
    {
        // clear data
        echo "Cleanup Lists\n";
        $this->db->table('lists')->truncate(); 
        $this->db->table('list_options')->truncate(); 
        
        echo "Populate Lists\n";
        $this->db->table('lists')->insert([
            "id" => 1,
            "name" => "user_options",
            "description" => "User Options",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 1,
            "name" => "language",
            "value" => "Language",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 1,
            "name" => "country",
            "value" => "Country",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 1,
            "name" => "timezone",
            "value" => "Time Zone",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 2,
            "name" => "default_user_options",
            "description" => "Default User Values",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 2,
            "name" => "language",
            "value" => "en",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 2,
            "name" => "country",
            "value" => "us",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 2,
            "name" => "timezone",
            "value" => "America/New_York",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 3,
            "name" => "country",
            "description" => "Countries",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 3,
            "name" => "us",
            "value" => "United States",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 3,
            "name" => "ca",
            "value" => "Canada",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 4,
            "name" => "language",
            "description" => "Languages",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 4,
            "name" => "en",
            "value" => "English",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 4,
            "name" => "es",
            "value" => "Spanish",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 5,
            "name" => "timezone",
            "description" => "Timezones",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 5,
            "name" => "America/New_York",
            "value" => "America/New_York",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 5,
            "name" => "America/Vancouver",
            "value" => "America/Vancouver",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 6,
            "name" => "app_notifications",
            "description" => "App Notifications",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 6,
            "name" => "1",
            "value" => "Yes",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 6,
            "name" => "2",
            "value" => "No",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $this->db->table('lists')->insert([
            "id" => 7,
            "name" => "email_notifications",
            "description" => "Email Notifications",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 7,
            "name" => "1",
            "value" => "Yes",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->db->table('list_options')->insert([
            "list_id" => 7,
            "name" => "2",
            "value" => "No",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

    }
}
