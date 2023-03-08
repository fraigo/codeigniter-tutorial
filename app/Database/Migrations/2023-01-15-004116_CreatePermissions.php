<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_type_id' => [
                'type'           => 'INT',
            ],
            'module' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
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
        $this->forge->addUniqueKey(['user_type_id','module']);
        if (getenv("database.foreighkeys")) $this->forge->addForeignKey('user_type_id', 'user_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permissions');
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}