<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPendingStatusToRegistrations extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE registrations DROP CONSTRAINT check_registration_status");
        $this->db->query("ALTER TABLE registrations ADD CONSTRAINT check_registration_status CHECK (status IN ('pending', 'confirmed', 'checked_in', 'cancelled', 'rejected'))");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE registrations DROP CONSTRAINT check_registration_status");
        $this->db->query("ALTER TABLE registrations ADD CONSTRAINT check_registration_status CHECK (status IN ('checked_in', 'confirmed', 'cancelled'))");
    }
}
