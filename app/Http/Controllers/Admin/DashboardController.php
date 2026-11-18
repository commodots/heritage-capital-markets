<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $user = request()->user();
        $stats = [];

        if ($user->hasFeature('homepage')) {
            $stats['heroSlides'] = HeroSlide::where('is_active', true)->count();
        }

        if ($user->hasFeature('market_indices')) {
            $stats['marketIndices'] = MarketIndex::where('is_active', true)->count();
        }

        if ($user->hasFeature('investment_solutions')) {
            $stats['investmentSolutions'] = InvestmentSolution::where('is_active', true)->count();
        }

        if ($user->hasFeature('research')) {
            $stats['researchReports'] = ResearchReport::where('is_published', true)->count();
        }

        if ($user->hasFeature('enquiries')) {
            $stats['enquiries'] = Enquiry::where('status', '!=', 'closed')->count();
        }

        if ($user->hasFeature('newsletter')) {
            $stats['newsletterSubscribers'] = NewsletterSubscriber::where('is_active', true)->count();
        }

        $recentEnquiries = $user->hasFeature('enquiries')
            ? Enquiry::query()
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'subject', 'status', 'created_at'])
            : collect();

        $latestHeroSlides = $user->hasFeature('homepage')
            ? HeroSlide::query()
                ->orderBy('sort_order')
                ->take(4)
                ->get(['id', 'title', 'image', 'is_active', 'sort_order'])
            : collect();

        $latestSolutions = $user->hasFeature('investment_solutions')
            ? InvestmentSolution::query()
                ->orderBy('sort_order')
                ->take(4)
                ->get(['id', 'title', 'featured_image', 'is_active', 'sort_order'])
            : collect();

        $latestReports = $user->hasFeature('research')
            ? ResearchReport::query()
                ->where('is_published', true)
                ->latest('published_at')
                ->take(4)
                ->get(['id', 'title', 'featured_image', 'published_at', 'is_published'])
            : collect();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentEnquiries' => $recentEnquiries,
            'latestHeroSlides' => $latestHeroSlides,
            'latestSolutions' => $latestSolutions,
            'latestReports' => $latestReports,
        ]);
    }
}