<?php

namespace Tests\Feature;

use App\Support\AdminFeatures;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemovedLiveChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_live_chat_routes_and_admin_permission_are_removed(): void
    {
        $this->get('/chat/availability')->assertNotFound();
        $this->postJson('/chat/start')->assertNotFound();
        $this->postJson('/chat/message')->assertNotFound();
        $this->getJson('/chat/messages')->assertNotFound();
        $this->postJson('/chat/offline-enquiry')->assertNotFound();
        $this->get('/admin/live-chat')->assertNotFound();
        $this->assertNotContains('live_chat', AdminFeatures::keys());
        $this->assertDatabaseHas('admin_roles', [
            'name' => 'Enquiry Support',
            'permissions' => json_encode(['dashboard', 'enquiries']),
        ]);
    }
}
