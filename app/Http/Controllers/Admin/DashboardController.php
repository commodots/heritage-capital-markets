<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Enquiry;
use App\Models\HeroSlide;
use App\Models\InvestmentSolution;
use App\Models\MarketIndex;
use App\Models\NewsletterSubscriber;
use App\Models\ResearchReport;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'heroSlides' => HeroSlide::where('is_active', true)->count(),

            'marketIndices' => MarketIndex::where('is_active', true)->count(),

            'investmentSolutions' => InvestmentSolution::where('is_active', true)->count(),

            'researchReports' => ResearchReport::where('is_published', true)->count(),

            'enquiries' => Enquiry::where('status', '!=', 'closed')->count(),

            'newsletterSubscribers' => NewsletterSubscriber::where('is_active', true)->count(),
        ];

        $recentEnquiries = Enquiry::query()
            ->latest()
            ->take(5)
            ->get([
                'id',
                'name',
                'email',
                'subject',
                'status',
                'created_at',
            ]);

        $latestHeroSlides = HeroSlide::query()
            ->orderBy('sort_order')
            ->take(4)
            ->get([
                'id',
                'title',
                'image',
                'is_active',
                'sort_order',
            ]);

        $latestSolutions = InvestmentSolution::query()
            ->orderBy('sort_order')
            ->take(4)
            ->get([
                'id',
                'title',
                'featured_image',
                'is_active',
                'sort_order',
            ]);

        $latestReports = ResearchReport::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->take(4)
            ->get([
                'id',
                'title',
                'featured_image',
                'published_at',
                'is_published',
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEnquiries' => $recentEnquiries,
            'latestHeroSlides' => $latestHeroSlides,
            'latestSolutions' => $latestSolutions,
            'latestReports' => $latestReports,
        ]);
    }
}