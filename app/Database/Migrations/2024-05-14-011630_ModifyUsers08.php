<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyUsers08 extends Migration
{
    public function up()
    {
        $fields = ([
            'birth_date' => [
                'type'           => 'DATE',
                'null'           => true,
            ]
        ]);
        
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users',['birth_date']);
    }
}