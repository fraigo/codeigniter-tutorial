<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 128,
            ],
            'password' => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
            ],
            'user_type' => [
                'type'           => 'INT',
                'default'        => '0',
            ],
            'avatar_url' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'login_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'password_token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
                'null'           => true,
            ],
            'password_token_expires' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'auth_token' => [
                'type'           => 'VARCHAR',
                'constraint'     => 128,
                'null'           => true,
            ],
            'phone' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
                'null'           => true,
            ],
            'address' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'city' => [
                'type'           => 'VARCHAR',
                'constraint'     => 128,
                'null'           => true,
            ],
            'postal_code' => [
                'type'           => 'VARCHAR',
                'constraint'     => 10,
                'null'           => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['email']);
        $this->forge->addKey(['name']);
        $this->forge->addForeignKey('user_type', 'user_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}