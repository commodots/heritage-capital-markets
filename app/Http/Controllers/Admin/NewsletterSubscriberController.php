<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterCampaignToSubscriber;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/NewsletterSubscribers/Index', [
            'subscribers' => NewsletterSubscriber::query()->latest()->get(),
            'activeSubscriberCount' => NewsletterSubscriber::subscribed()->count(),
        ]);
    }

    public function toggle(NewsletterSubscriber $newsletterSubscriber)
    {
        if ($newsletterSubscriber->is_active) {
            $newsletterSubscriber->update(['is_active' => false]);

            return back()->with('success', 'Subscriber deactivated.');
        }

        if ($newsletterSubscriber->unsubscribed_at) {
            return back()->with('error', 'This subscriber opted out. They must subscribe again themselves before receiving future emails.');
        }

        $newsletterSubscriber->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'subscription_version' => $newsletterSubscriber->subscriptionVersion() + 1,
        ]);

        return back()->with('success', 'Subscriber reactivated.');
    }

    public function sendCampaign(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
        ]);

        $queued = 0;
        NewsletterSubscriber::subscribed()
            ->select('id')
            ->orderBy('id')
            ->chunkById(500, function ($subscribers) use ($validated, &$queued) {
                foreach ($subscribers as $subscriber) {
                    SendNewsletterCampaignToSubscriber::dispatch(
                        $subscriber->id,
                        $validated['subject'],
                        $validated['body'],
                    );
                    $queued++;
                }
            });

        if ($queued === 0) {
            return back()->with('error', 'There are no active subscribers to receive this campaign.');
        }

        return back()->with('success', "Campaign scheduled for delivery to {$queued} active subscriber(s).");
    }

    public function export(): StreamedResponse
    {
        $filename = 'heritage-newsletter-subscribers-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'name', 'status', 'subscribed_at', 'unsubscribed_at', 'created_at']);

            NewsletterSubscriber::query()->orderBy('email')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $subscriber) {
                    fputcsv($out, [
                        $subscriber->email,
                        $subscriber->name,
                        $subscriber->status,
                        $subscriber->subscribed_at,
                        $subscriber->unsubscribed_at,
                        $subscriber->created_at,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
