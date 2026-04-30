<?php

namespace Tests\Controllers;

use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\TestCase;

class AdminAccessTest extends TestCase
{
    use FeatureTestTrait;

    public function testUnauthenticatedRedirectsToLogin()
    {
        $result = $this->get('/admin/dashboard');
        $result->assertRedirectTo('/login');
    }

    public function testAuthenticatedCanAccessDashboard()
    {
        $result = $this->withSession([
            'user_id' => $this->getTestUserId(),
            'user_name' => 'Admin',
            'user_role' => 'admin',
            'logged_in' => true,
        ])->get('/admin/dashboard');

        $result->assertStatus(200);
        $result->assertSee('Dashboard');
    }

    public function testAuthenticatedCanAccessEventList()
    {
        $result = $this->withSession([
            'user_id' => $this->getTestUserId(),
            'user_name' => 'Admin',
            'user_role' => 'admin',
            'logged_in' => true,
        ])->get('/admin/events');

        $result->assertStatus(200);
    }
}