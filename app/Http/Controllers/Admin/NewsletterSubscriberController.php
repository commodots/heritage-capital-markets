<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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
        $filename='newsletter-subscribers-'.date('Y-m-d').'.csv';
        return response()->streamDownload(function () {
            $out=fopen('php://output','w');
            fputcsv($out,['Name','Email','Active','Subscribed At']);
            NewsletterSubscriber::query()->orderBy('email')->chunk(500,function($rows) use($out){
                foreach($rows as $row) fputcsv($out,[$row->name ?? '',$row->email,$row->is_active ? 'Yes':'No',$row->created_at]);
            });
            fclose($out);
        },$filename,['Content-Type'=>'text/csv']);
    }
}
