<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLists extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'description' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'active' => [
                'type'           => 'INT',
                'constraint'     => 255,
                'default'        => '1',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['name']);
        
        $this->forge->createTable('lists');
    }

    public function down()
    {
        $this->forge->dropTable('lists');
    }
}