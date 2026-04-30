<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'event_id' => ['type' => 'INT'],
            'ticket_type_id' => ['type' => 'INT'],
            'confirmation_code' => ['type' => 'VARCHAR', 'constraint' => 20],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'company' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'confirmed'],
            'checked_in_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'qr_code_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('confirmation_code');
        $this->forge->addKey('event_id');
        $this->forge->addKey('email');
        $this->forge->addForeignKey('event_id', 'events', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('ticket_type_id', 'ticket_types', 'id');
        $this->forge->createTable('registrations');

        $this->db->query("ALTER TABLE registrations ADD CONSTRAINT check_registration_status CHECK (status IN ('checked_in', 'confirmed', 'cancelled'))");
    }

    public function down()
    {
        $this->forge->dropTable('registrations');
    }
}