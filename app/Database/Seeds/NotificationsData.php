<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationsData extends Seeder
{
    public function run()
    {
        // clear data
        echo "Cleanup Notifications\n";
        $this->db->table('notifications')->truncate(); 
        echo "Cleanup User Notifications\n";
        $this->db->table('user_notifications')->truncate(); 

    }
}
