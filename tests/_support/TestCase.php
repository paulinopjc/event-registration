<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

abstract class TestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean all tables before each test (CASCADE required for PostgreSQL foreign keys)
        $db = \Config\Database::connect('tests');
        $db->query('TRUNCATE TABLE custom_field_values, custom_fields, registrations, ticket_types, events, users RESTART IDENTITY CASCADE');

        // Seed a test user
        $db->table('users')->insert([
            'name' => 'Test Admin',
            'email' => 'test@test.com',
            'role' => 'admin',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function db()
    {
        return \Config\Database::connect('tests');
    }

    protected function getTestUserId(): int
    {
        $user = $this->db()->table('users')->where('email', 'test@test.com')->get()->getRowArray();
        return (int) $user['id'];
    }

    protected function createEvent(array $overrides = []): int
    {
        $data = array_merge([
            'user_id' => $this->getTestUserId(),
            'name' => 'Test Event',
            'slug' => 'test-event-' . uniqid(),
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $overrides);
        $this->db()->table('events')->insert($data);
        return (int) $this->db()->insertID();
    }

    protected function createTicketType(int $eventId, array $overrides = []): int
    {
        $data = array_merge([
            'event_id' => $eventId,
            'name' => 'General',
            'price' => 0,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
        ], $overrides);
        $this->db()->table('ticket_types')->insert($data);
        return (int) $this->db()->insertID();
    }

    protected function createRegistration(int $eventId, int $ticketTypeId, array $overrides = []): int
    {
        $data = array_merge([
            'event_id' => $eventId,
            'ticket_type_id' => $ticketTypeId,
            'confirmation_code' => 'EVT-' . strtoupper(substr(uniqid(), -6)),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@test.com',
            'status' => 'confirmed',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], $overrides);
        $this->db()->table('registrations')->insert($data);
        return (int) $this->db()->insertID();
    }
}
