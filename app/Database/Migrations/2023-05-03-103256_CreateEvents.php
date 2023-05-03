<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'type' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'reference' => [
                'type'           => 'INT',
                'null'           => true,
            ],
            'created_by' => [
                'type'           => 'INT',
                'null'           => true,
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
        $this->forge->addKey(['type']);
        if (getenv("database.foreignkeys")) $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('events');
    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}