<?php

namespace Tests\Models;

use Tests\Support\TestCase;

class EventModelTest extends TestCase
{
    public function testCreateEvent()
    {
        $this->createEvent([
            'name' => 'Test Conference',
            'slug' => 'test-conference',
            'status' => 'draft',
        ]);

        $event = $this->db()->table('events')->where('slug', 'test-conference')->get()->getRowArray();
        $this->assertNotNull($event);
        $this->assertEquals('Test Conference', $event['name']);
        $this->assertEquals('draft', $event['status']);
    }

    public function testGetBySlug()
    {
        $this->createEvent(['name' => 'Slug Test', 'slug' => 'slug-test']);

        $event = $this->db()->table('events')->where('slug', 'slug-test')->get()->getRowArray();
        $this->assertNotNull($event);
        $this->assertEquals('Slug Test', $event['name']);
    }

    public function testGetBySlugReturnsNullForMissing()
    {
        $event = $this->db()->table('events')->where('slug', 'does-not-exist')->get()->getRowArray();
        $this->assertNull($event);
    }

    public function testGenerateSlugFromTitle()
    {
        $slug = url_title('My Great Event 2026', '-', true);
        $this->assertEquals('my-great-event-2026', $slug);
    }
}
