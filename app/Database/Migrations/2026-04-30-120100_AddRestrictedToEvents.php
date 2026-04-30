<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRestrictedToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'is_restricted' => ['type' => 'BOOLEAN', 'default' => false],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'is_restricted');
    }
}
