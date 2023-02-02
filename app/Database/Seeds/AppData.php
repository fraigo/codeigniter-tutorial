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

    }
}
