<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LiveChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_offline_enquiry_is_available_without_login_and_stored_for_admin_follow_up(): void
    {
        $this->postJson('/chat/offline-enquiry', [
            'name' => 'Amina Example',
            'email' => 'amina@example.test',
            'phone' => '+2348000000000',
            'message' => 'Please contact me about opening an account.',
        ])->assertCreated()
            ->assertJsonPath('message', 'Thank you. Your enquiry has been received and our team will contact you.');

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Amina Example',
            'email' => 'amina@example.test',
            'subject' => 'Live Chat Offline Enquiry',
            'source' => 'live_chat_offline',
            'status' => 'new',
        ]);
    }

    public function test_live_chat_administrator_can_open_inbox_and_read_unread_count(): void
    {
        $role = AdminRole::create([
            'name' => 'Chat Support Test',
            'permissions' => ['dashboard', 'live_chat'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);
        $conversation = ChatConversation::create([
            'visitor_token' => str_repeat('a', 64),
            'visitor_name' => 'Visitor Example',
            'visitor_email' => 'visitor@example.test',
            'status' => 'waiting',
            'last_message_at' => now(),
        ]);
        $conversation->messages()->create([
            'sender_type' => 'visitor',
            'message' => 'I need help.',
        ]);

        $this->actingAs($admin)
            ->get('/admin/live-chat')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/LiveChat/Index')
                ->where('unreadCount', 1)
                ->has('conversations', 1));

        $this->actingAs($admin)
            ->get('/admin/live-chat/unread-count')
            ->assertOk()
            ->assertJson(['count' => 1]);
    }

    public function test_administrator_without_live_chat_permission_is_denied(): void
    {
        $role = AdminRole::create([
            'name' => 'Pages Only Test',
            'permissions' => ['dashboard', 'pages'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)->get('/admin/live-chat')->assertForbidden();
    }
}
