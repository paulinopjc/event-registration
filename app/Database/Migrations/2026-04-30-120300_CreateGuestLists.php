<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuestLists extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'event_id'        => ['type' => 'INT'],
            'first_name'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'last_name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_registered'   => ['type' => 'BOOLEAN', 'default' => false],
            'registration_id' => ['type' => 'INT', 'null' => true],
            'created_at'      => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('event_id');
        $this->forge->addForeignKey('event_id', 'events', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', '', 'SET NULL');
        $this->forge->createTable('guest_lists');

        $this->db->query('CREATE UNIQUE INDEX idx_guest_event_email ON guest_lists (event_id, email)');
    }

    public function down()
    {
        $this->forge->dropTable('guest_lists');
    }
}
