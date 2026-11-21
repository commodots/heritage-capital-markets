<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'website' => ['prohibited'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'website.prohibited' => 'Unable to process this subscription.',
        ]);

        $subscriber = NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $validated['email']],
            ['is_active' => true, 'subscribed_at' => now()],
        );

        if ($subscriber->wasRecentlyCreated) {
            $message = 'Thank you. You have successfully subscribed to Heritage Capital Markets updates.';
        } elseif (! $subscriber->is_active) {
            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'subscription_version' => $subscriber->subscriptionVersion() + 1,
            ]);
            $message = 'Your newsletter subscription has been reactivated.';
        } else {
            $message = 'This email is already subscribed to our newsletter.';
        }

        return back()->with('success', $message);
    }

    public function confirmUnsubscribe(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        abort_unless(
            (string) $request->query('version') === (string) $newsletterSubscriber->subscriptionVersion(),
            403,
        );

        return Inertia::render('Newsletter/Unsubscribe', [
            'email' => $newsletterSubscriber->email,
            'isSubscribed' => $newsletterSubscriber->is_active,
            'actionUrl' => $request->fullUrl(),
        ]);
    }

    public function unsubscribe(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        abort_unless(
            (string) $request->query('version') === (string) $newsletterSubscriber->subscriptionVersion(),
            403,
        );

        if ($newsletterSubscriber->is_active) {
            $newsletterSubscriber->update([
                'is_active' => false,
                'unsubscribed_at' => now(),
            ]);
        }

        return redirect()->to(URL::signedRoute('newsletter.unsubscribe.confirm', [
            'newsletterSubscriber' => $newsletterSubscriber->getKey(),
            'version' => $newsletterSubscriber->subscriptionVersion(),
        ]))->with('success', 'You have been unsubscribed from Heritage Capital Markets updates.');
    }
}
