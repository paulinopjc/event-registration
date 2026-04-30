<?php

namespace Tests\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\TestCase;

class PublicRegistrationTest extends TestCase
{
    use FeatureTestTrait;

    public function testPublicEventPageShowsPublishedEvent()
    {
        $eventId = $this->createEvent(['name' => 'Public Event', 'slug' => 'public-event']);
        $this->createTicketType($eventId, ['name' => 'Free']);

        $result = $this->get('/event/public-event');
        $result->assertStatus(200);
        $result->assertSee('Public Event');
    }

    public function testPublicEventPageReturns404ForDraft()
    {
        $this->createEvent(['name' => 'Draft Event', 'slug' => 'draft-event', 'status' => 'draft']);

        $this->expectException(PageNotFoundException::class);
        $this->get('/event/draft-event');
    }

    public function testCapacityBlocksRegistrationWhenFull()
    {
        $eventId = $this->createEvent(['name' => 'Full Event', 'slug' => 'full-event']);
        $ticketId = $this->createTicketType($eventId, [
            'name' => 'Limited',
            'capacity' => 1,
        ]);

        $this->createRegistration($eventId, $ticketId, [
            'first_name' => 'First',
            'last_name' => 'Person',
            'email' => 'first@test.com',
        ]);

        $result = $this->post("/event/full-event/register", [
            'first_name' => 'Second',
            'last_name' => 'Person',
            'email' => 'second@test.com',
            'ticket_type_id' => $ticketId,
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error', 'This ticket type is sold out');
    }
}
