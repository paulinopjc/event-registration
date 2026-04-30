<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'google_sub' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'admin'],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        $this->db->query("ALTER TABLE users ADD CONSTRAINT check_user_role CHECK (role IN ('admin', 'staff'))");
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}