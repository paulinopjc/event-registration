<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUserRoles extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users DROP CONSTRAINT check_user_role");
        $this->db->query("UPDATE users SET role = 'editor' WHERE role = 'staff'");
        $this->db->query("ALTER TABLE users ADD CONSTRAINT check_user_role CHECK (role IN ('admin', 'editor', 'viewer'))");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE users DROP CONSTRAINT check_user_role");
        $this->db->query("UPDATE users SET role = 'staff' WHERE role = 'editor'");
        $this->db->query("ALTER TABLE users ADD CONSTRAINT check_user_role CHECK (role IN ('admin', 'staff'))");
    }
}
