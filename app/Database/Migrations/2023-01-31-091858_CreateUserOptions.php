<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserOptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
            ],
            'option' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
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
        $this->forge->addUniqueKey(['user_id','option']);
        //$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_options');
    }

    public function down()
    {
        $this->forge->dropTable('user_options');
    }
}