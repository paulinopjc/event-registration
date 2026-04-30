<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'user_id' => ['type' => 'INT'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 500],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 500],
            'description' => ['type' => 'TEXT', 'null' => true],
            'venue' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'event_date' => ['type' => 'TIMESTAMP', 'null' => true],
            'event_end_date' => ['type' => 'TIMESTAMP', 'null' => true],
            'banner_image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'draft'],
            'max_registrations' => ['type' => 'INT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->createTable('events');

        $this->db->query("ALTER TABLE events ADD CONSTRAINT check_event_status CHECK (status IN ('draft', 'published', 'archived'))");
    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}