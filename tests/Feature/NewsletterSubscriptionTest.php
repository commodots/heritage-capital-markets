<?php

namespace Tests\Feature;

use App\Jobs\SendNewsletterCampaignToSubscriber;
use App\Mail\NewsletterCampaignEmail;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);
    }

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
        $this->assertNotNull(NewsletterSubscriber::first()->subscribed_at);
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
            'unsubscribed_at' => now()->subDay(),
        ]);
        $oldVersion = $subscriber->subscriptionVersion();

        $response = $this->from('/')->post('/newsletter/subscribe', [
            'email' => 'reader@example.com',
            'website' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Your newsletter subscription has been reactivated.');
        $this->assertDatabaseHas('newsletter_subscribers', [
            'id' => $subscriber->id,
            'is_active' => true,
            'unsubscribed_at' => null,
        ]);
        $this->assertGreaterThan($oldVersion, $subscriber->fresh()->subscriptionVersion());
        $this->assertNotNull($subscriber->fresh()->subscribed_at);
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

    public function test_a_signed_unsubscribe_link_confirms_and_processes_without_login(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);
        $url = $subscriber->unsubscribeUrl();

        $this->get($url)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Newsletter/Unsubscribe')
                ->where('email', 'reader@example.com')
                ->where('isSubscribed', true));

        $this->post($url)->assertRedirect();

        $this->assertFalse($subscriber->fresh()->is_active);
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
        $this->assertSame('unsubscribed', $subscriber->fresh()->status);
    }

    public function test_unsubscribe_links_must_be_signed_and_are_invalidated_after_resubscription(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);
        $url = $subscriber->unsubscribeUrl();

        $this->get('/newsletter/unsubscribe/'.$subscriber->id.'?version='.$subscriber->subscriptionVersion())
            ->assertForbidden();

        $this->post($url)->assertRedirect();
        $this->from('/')->post('/newsletter/subscribe', [
            'email' => 'reader@example.com',
            'website' => '',
        ])->assertRedirect('/');

        $this->get($url)->assertForbidden();
    }

    public function test_admin_campaigns_are_queued_only_for_active_subscribers(): void
    {
        Queue::fake();
        $active = NewsletterSubscriber::create([
            'email' => 'active@example.com',
            'is_active' => true,
        ]);
        NewsletterSubscriber::create([
            'email' => 'unsubscribed@example.com',
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
        NewsletterSubscriber::create([
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/newsletter-subscribers/campaign', [
                'subject' => 'Market update',
                'body' => 'This is a test campaign.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Campaign scheduled for delivery to 1 active subscriber(s).');

        Queue::assertPushed(SendNewsletterCampaignToSubscriber::class, 1);
        Queue::assertPushed(SendNewsletterCampaignToSubscriber::class, fn ($job) => $job->subscriberId === $active->id);
    }

    public function test_queued_campaign_delivery_rechecks_subscription_and_includes_unsubscribe_link(): void
    {
        Mail::fake();
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);
        $job = new SendNewsletterCampaignToSubscriber($subscriber->id, 'Market update', 'Hello there.');

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
        $job->handle();
        Mail::assertNothingSent();

        $subscriber->update([
            'is_active' => true,
            'unsubscribed_at' => null,
            'subscription_version' => $subscriber->subscriptionVersion() + 1,
        ]);
        $job->handle();

        Mail::assertSent(NewsletterCampaignEmail::class, function (NewsletterCampaignEmail $mail) use ($subscriber) {
            return $mail->hasTo($subscriber->email)
                && str_contains($mail->render(), $mail->unsubscribeUrl);
        });
    }

    public function test_admin_export_includes_status_and_unsubscribe_timestamp(): void
    {
        NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin())
            ->get('/admin/newsletter-subscribers/export')
            ->assertOk();

        $csv = $response->streamedContent();
        $this->assertStringContainsString('unsubscribed_at', $csv);
        $this->assertStringContainsString('unsubscribed', $csv);
        $this->assertStringContainsString('reader@example.com', $csv);
    }
}
