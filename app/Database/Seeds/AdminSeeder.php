<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $existing = $this->db->table('users')
            ->where('email', 'paulinopjc@gmail.com')
            ->get()
            ->getRow();

        if ($existing) {
            return;
        }

        $this->db->table('users')->insert([
            'name' => 'Paulino Awino',
            'email' => 'paulinopjc@gmail.com',
            'google_sub' => null,
            'role' => 'admin',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}