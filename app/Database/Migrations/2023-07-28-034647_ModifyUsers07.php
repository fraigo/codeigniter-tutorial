<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyUsers07 extends Migration
{
    public function up()
    {
        $fields = ([
            'push_token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ]
        ]);
        
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users',['push_token']);
    }
}