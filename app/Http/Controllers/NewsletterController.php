<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

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
            ['is_active' => true],
        );

        if ($subscriber->wasRecentlyCreated) {
            $message = 'Thank you. You have successfully subscribed to Heritage Capital Markets updates.';
        } elseif (! $subscriber->is_active) {
            $subscriber->update(['is_active' => true]);
            $message = 'Your newsletter subscription has been reactivated.';
        } else {
            $message = 'This email is already subscribed to our newsletter.';
        }

        return back()->with('success', $message);
    }
}
