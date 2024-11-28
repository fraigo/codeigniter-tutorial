<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPages2305 extends Migration
{
    public function up()
    {
        $fields = ([
            'category' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
        ]);
        
        $this->forge->addColumn('pages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pages',['category']);
    }
}