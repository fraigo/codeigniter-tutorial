<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTypes extends Migration
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
                'constraint'     => 255,
            ],
            'access' => [
                'type'           => 'INT',
                'default'        => '0',
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
        
        $this->forge->createTable('user_types');
    }

    public function down()
    {
        $this->forge->dropTable('user_types');
    }
}