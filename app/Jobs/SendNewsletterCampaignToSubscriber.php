<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignEmail;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterCampaignToSubscriber implements ShouldBeEncrypted, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 1800];

    public function __construct(
        public int $subscriberId,
        public string $campaignSubject,
        public string $body,
    ) {}

    public function handle(): void
    {
        $subscriber = NewsletterSubscriber::subscribed()->find($this->subscriberId);

        if (! $subscriber) {
            return;
        }

        Mail::to($subscriber->email)->send(new NewsletterCampaignEmail(
            campaignSubject: $this->campaignSubject,
            body: $this->body,
            unsubscribeUrl: $subscriber->unsubscribeUrl(),
            subscriberName: $subscriber->name,
        ));
    }
}
