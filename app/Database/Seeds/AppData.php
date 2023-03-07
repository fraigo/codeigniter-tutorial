<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Config\Services;

class AppData extends Seeder
{
    public function run()
    {
        $seed = new ListsData($this->config);
        $seed->run();
        $seed = new UsersData($this->config);
        $seed->run();
        echo "Add Pages\n";
        $this->db->table('pages')->truncate();
        $this->db->table('pages')->insert([
            "id" => 1,
            "title" => "Home",
            "description" => "Home Page",
            "contents" => "<h1>Home Page</h1>\n",
            "slug" => "home",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);

    }
}
