<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_normalized_newsletter_subscription(): void
    {
        $response = $this->from('/')->post('/newsletter/subscribe', [
            'email' => '  TEST.User@Example.com ',
            'website' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'test.user@example.com',
            'is_active' => true,
        ]);
    }

    public function test_an_active_duplicate_gets_a_friendly_message_without_a_duplicate(): void
    {
        NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => true,
        ]);

        $response = $this->from('/')->post('/newsletter/subscribe', [
            'email' => ' READER@example.com ',
            'website' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'This email is already subscribed to our newsletter.');
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_an_inactive_subscription_is_reactivated(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => false,
        ]);

        $response = $this->from('/')->post('/newsletter/subscribe', [
            'email' => 'reader@example.com',
            'website' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Your newsletter subscription has been reactivated.');
        $this->assertDatabaseHas('newsletter_subscribers', [
            'id' => $subscriber->id,
            'is_active' => true,
        ]);
    }

    public function test_it_rejects_invalid_emails_and_filled_honeypot_fields(): void
    {
        $this->from('/')->post('/newsletter/subscribe', [
            'email' => 'not-an-email',
            'website' => '',
        ])->assertSessionHasErrors('email');

        $this->from('/')->post('/newsletter/subscribe', [
            'email' => 'reader@example.com',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_public_subscriptions_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/newsletter/subscribe', [
                'email' => "reader{$attempt}@example.com",
                'website' => '',
            ])->assertRedirect();
        }

        $this->post('/newsletter/subscribe', [
            'email' => 'sixth@example.com',
            'website' => '',
        ])->assertTooManyRequests();
    }

    public function test_admin_newsletter_export_is_protected(): void
    {
        $this->get('/admin/newsletter-subscribers/export')
            ->assertRedirect('/login');
    }
}
