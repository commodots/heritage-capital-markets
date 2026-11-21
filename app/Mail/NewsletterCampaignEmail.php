<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $campaignSubject,
        public string $body,
        public string $unsubscribeUrl,
        public ?string $subscriberName,
    ) {}

    public function build(): self
    {
        return $this
            ->subject($this->campaignSubject)
            ->view('emails.newsletter-campaign')
            ->text('emails.newsletter-campaign-text');
    }
}
