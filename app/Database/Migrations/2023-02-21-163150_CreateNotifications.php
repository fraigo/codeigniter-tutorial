<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotifications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'content' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'icon' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
                'null'           => true,
            ],
            'active' => [
                'type'           => 'TINYINT',
                'default'        => '1',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'link' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        
        
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}