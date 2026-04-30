<?php

namespace Tests\Models;

use App\Models\RegistrationModel;
use Tests\Support\TestCase;

class RegistrationModelTest extends TestCase
{
    private function seedEventAndTicket(): array
    {
        $eventId = $this->createEvent();
        $ticketId = $this->createTicketType($eventId);
        return [$eventId, $ticketId];
    }

    public function testGenerateCodeFormat()
    {
        $code = RegistrationModel::generateCode();
        $this->assertMatchesRegularExpression('/^EVT-[A-Z0-9]{6}$/', $code);
    }

    public function testGenerateCodeIsUnique()
    {
        $codes = [];
        for ($i = 0; $i < 100; $i++) {
            $codes[] = RegistrationModel::generateCode();
        }
        $this->assertCount(100, array_unique($codes));
    }

    public function testGetByCode()
    {
        [$eventId, $ticketId] = $this->seedEventAndTicket();

        $this->createRegistration($eventId, $ticketId, [
            'confirmation_code' => 'EVT-TEST01',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
        ]);

        $reg = $this->db()->table('registrations')
            ->where('confirmation_code', 'EVT-TEST01')
            ->get()->getRowArray();
        $this->assertNotNull($reg);
        $this->assertEquals('John', $reg['first_name']);
    }

    public function testGetByCodeReturnsNullForMissing()
    {
        $reg = $this->db()->table('registrations')
            ->where('confirmation_code', 'EVT-NOPE00')
            ->get()->getRowArray();
        $this->assertNull($reg);
    }

    public function testCountByTicketType()
    {
        [$eventId, $ticketId] = $this->seedEventAndTicket();

        for ($i = 0; $i < 3; $i++) {
            $this->createRegistration($eventId, $ticketId, [
                'first_name' => 'User',
                'last_name' => "Number$i",
                'email' => "user$i@test.com",
                'status' => 'confirmed',
            ]);
        }

        $count = $this->db()->table('registrations')
            ->where('event_id', $eventId)
            ->where('ticket_type_id', $ticketId)
            ->where('status !=', 'cancelled')
            ->countAllResults();
        $this->assertEquals(3, $count);
    }
}
