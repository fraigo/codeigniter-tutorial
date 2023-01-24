<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListOptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'list_id' => [
                'type'           => 'INT',
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
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
        $this->forge->addKey(['list_id','name']);
        
        $this->forge->createTable('list_options');
    }

    public function down()
    {
        $this->forge->dropTable('list_options');
    }
}