<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\InvestmentSolution;
use App\Models\MarketIndex;
use App\Models\SiteSetting;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $solutions = InvestmentSolution::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $markets = MarketIndex::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $settings = SiteSetting::query()
            ->pluck('value', 'key');

        return Inertia::render('Home', [
            'heroSlides' => $heroSlides,
            'solutions' => $solutions,
            'markets' => $markets,
            'settings' => $settings,
        ]);
    }
}