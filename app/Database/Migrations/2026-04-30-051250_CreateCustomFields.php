<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomFields extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'event_id' => ['type' => 'INT'],
            'label' => ['type' => 'VARCHAR', 'constraint' => 255],
            'field_type' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'text'],
            'options' => ['type' => 'JSON', 'null' => true],
            'is_required' => ['type' => 'BOOLEAN','default' => false],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('event_id');
        $this->forge->addForeignKey('event_id', 'events', 'id', '', 'CASCADE');
        $this->forge->createTable('custom_fields');

        $this->db->query("ALTER TABLE custom_fields ADD CONSTRAINT check_field_type CHECK (field_type IN ('text', 'textarea', 'dropdown', 'checkbox', 'radio'))");

        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'registration_id' => ['type' => 'INT'],
            'custom_field_id' => ['type' => 'INT'],
            'value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('registration_id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('custom_field_id', 'custom_fields', 'id', '', 'CASCADE');
        $this->forge->createTable('custom_field_values');
    }

    public function down()
    {
        $this->forge->dropTable('custom_field_values');
        $this->forge->dropTable('custom_fields');
    }
}