<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;

class NewsletterSubscriberController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/NewsletterSubscribers/Index', [
            'subscribers'=>NewsletterSubscriber::query()->latest()->get(),
        ]);
    }

    public function toggle(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->update(['is_active'=>!$newsletterSubscriber->is_active]);
        return back()->with('success','Subscriber status updated.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'heritage-newsletter-subscribers-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'status', 'subscribed_at']);

            NewsletterSubscriber::query()->orderBy('email')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $subscriber) {
                    fputcsv($out, [
                        $subscriber->email,
                        $subscriber->is_active ? 'active' : 'inactive',
                        $subscriber->created_at,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
