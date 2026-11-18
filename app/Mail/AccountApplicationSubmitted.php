<?php

namespace App\Mail;

use App\Models\AccountApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountApplicationSubmitted extends Mailable implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = [60, 300, 900, 1800];

    public function __construct(
        public AccountApplication $application,
        public string $pdfContent,
        public string $pdfUrl,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('New account application: '.$this->application->reference)
            ->view('emails.account-application-submitted')
            ->attachData(
                $this->pdfContent,
                $this->application->reference.'.pdf',
                ['mime' => 'application/pdf'],
            );
    }
}
