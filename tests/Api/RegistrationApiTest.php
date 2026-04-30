<?php

namespace Tests\Api;

use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\TestCase;

class RegistrationApiTest extends TestCase
{
    use FeatureTestTrait;

    private function seedRegistration(string $code = 'EVT-API001', string $status = 'confirmed'): void
    {
        $eventId = $this->createEvent(['name' => 'API Test Event', 'slug' => 'api-test-event-' . uniqid()]);
        $ticketId = $this->createTicketType($eventId, ['name' => 'Standard']);
        $this->createRegistration($eventId, $ticketId, [
            'confirmation_code' => $code,
            'status' => $status,
        ]);
    }

    public function testShowReturnsRegistration()
    {
        $this->seedRegistration('EVT-SHOW01');

        $result = $this->get('/api/registrations/EVT-SHOW01');

        $result->assertStatus(200);
        $json = json_decode($result->getJSON(), true);
        $this->assertEquals('EVT-SHOW01', $json['data']['confirmation_code']);
        $this->assertEquals('Jane Smith', $json['data']['name']);
    }

    public function testShowReturns404ForInvalidCode()
    {
        $result = $this->get('/api/registrations/EVT-INVALID');

        $result->assertStatus(404);
    }

    public function testCheckinMarksAsCheckedIn()
    {
        $this->seedRegistration('EVT-CHKIN1');

        $result = $this->post('/api/registrations/EVT-CHKIN1/checkin');

        $result->assertStatus(200);
        $result->assertJSONFragment(['message' => 'Checked in successfully']);

        $reg = $this->db()->table('registrations')->where('confirmation_code', 'EVT-CHKIN1')->get()->getRowArray();
        $this->assertEquals('checked_in', $reg['status']);
        $this->assertNotNull($reg['checked_in_at']);
    }

    public function testDoubleCheckinReturns409()
    {
        $this->seedRegistration('EVT-DBL001', 'checked_in');

        $result = $this->post('/api/registrations/EVT-DBL001/checkin');

        $result->assertStatus(409);
    }

    public function testCheckinCancelledReturns400()
    {
        $this->seedRegistration('EVT-CAN001', 'cancelled');

        $result = $this->post('/api/registrations/EVT-CAN001/checkin');

        $result->assertStatus(400);
    }

    public function testEventStatsReturnsCorrectCounts()
    {
        $eventId = $this->createEvent(['name' => 'Stats Event', 'slug' => 'stats-event']);
        $ticketId = $this->createTicketType($eventId, [
            'name' => 'VIP',
            'price' => 100,
            'capacity' => 50,
        ]);

        $this->createRegistration($eventId, $ticketId, [
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => 'a@test.com',
            'status' => 'confirmed',
        ]);
        $this->createRegistration($eventId, $ticketId, [
            'first_name' => 'C',
            'last_name' => 'D',
            'email' => 'c@test.com',
            'status' => 'checked_in',
        ]);

        $result = $this->get("/api/events/$eventId/stats");

        $result->assertStatus(200);
        $json = json_decode($result->getJSON(), true);
        $this->assertEquals(2, $json['data']['total_registrations']);
        $this->assertEquals(1, $json['data']['checked_in']);
    }
}
