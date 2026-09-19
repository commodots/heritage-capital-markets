<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\InvestmentSolution;
use App\Models\MarketIndex;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hero Slides
        |--------------------------------------------------------------------------
        */

        HeroSlide::query()->delete();

        $heroSlides = [
            [
                'eyebrow' => 'BUILDING WEALTH. CREATING VALUE.',
                'title' => 'Your Trusted Partner in Investment Excellence.',
                'description' => 'Innovative investment solutions, market expertise and a commitment to your financial future.',
                'image' => 'images/hero/slide-01-coins-tree.webp',
                'primary_label' => 'Explore Our Solutions',
                'primary_url' => '/investment-solutions',
                'secondary_label' => 'Open an Account',
                'secondary_url' => '/open-account',
                'location_label' => 'Heritage',
                'location_text' => 'The Heritage Place',
                'is_active' => true,
                'sort_order' => 1,
            ],

            [
                'eyebrow' => 'INSIGHTS THAT MATTER.',
                'title' => 'Turning Insight Into Opportunity.',
                'description' => 'Access expert analysis, market intelligence and actionable insights to stay ahead.',
                'image' => 'images/hero/slide-02-market-insights.webp',
                'primary_label' => 'View Market Insights',
                'primary_url' => '/research-centre/market-overview',
                'secondary_label' => 'Research Reports',
                'secondary_url' => '/research-centre/reports',
                'location_label' => 'Heritage',
                'location_text' => 'Market Intelligence',
                'is_active' => true,
                'sort_order' => 2,
            ],

            [
                'eyebrow' => 'A LEGACY OF TRUST.',
                'title' => 'Partnering for Progress.',
                'description' => 'Backed by experience, driven by innovation and committed to your success.',
                'image' => 'images/hero/slide-03-heritage-reception.webp',
                'primary_label' => 'About Us',
                'primary_url' => '/about-us',
                'secondary_label' => 'Our Commitment',
                'secondary_url' => '/about-us',
                'location_label' => 'Heritage',
                'location_text' => 'The Heritage Place',
                'is_active' => true,
                'sort_order' => 3,
            ],

            [
                'eyebrow' => 'YOUR GOALS. OUR COMMITMENT.',
                'title' => 'A More Secure Financial Tomorrow.',
                'description' => 'Personalised investment solutions to help you achieve what matters most.',
                'image' => 'images/hero/slide-04-financial-future.webp',
                'primary_label' => 'Open an Account',
                'primary_url' => '/open-account',
                'secondary_label' => 'Speak to an Advisor',
                'secondary_url' => '/contact-us',
                'location_label' => 'Heritage',
                'location_text' => 'Your Financial Future',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($heroSlides as $slide) {
            HeroSlide::create($slide);
        }


        /*
        |--------------------------------------------------------------------------
        | Investment Solutions
        |--------------------------------------------------------------------------
        */

        InvestmentSolution::query()->delete();

        $solutions = [
            [
                'title' => 'Portfolio Management',
                'slug' => 'portfolio-management',
                'short_description' => 'Strategic investment solutions designed around your goals.',
                'description' => 'Professional portfolio management solutions designed around your objectives, risk profile and investment horizon.',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'is_active' => true,
                'sort_order' => 1,
            ],

            [
                'title' => 'Investment Advisory',
                'slug' => 'investment-advisory',
                'short_description' => 'A dedicated team with deep market knowledge.',
                'description' => 'Research-driven investment advisory services to help clients make informed financial decisions.',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'is_active' => true,
                'sort_order' => 2,
            ],

            [
                'title' => 'A More Secure Future',
                'slug' => 'financial-planning',
                'short_description' => 'Helping you build and preserve wealth for generations.',
                'description' => 'Financial planning solutions designed to help you build, protect and preserve wealth over the long term.',
                'featured_image' => 'images/hero/slide-04-financial-future.webp',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($solutions as $solution) {
            InvestmentSolution::create($solution);
        }


        /*
        |--------------------------------------------------------------------------
        | Market Indices
        |--------------------------------------------------------------------------
        |
        | These are the existing homepage demonstration figures.
        | We will later replace them with live market data.
        |--------------------------------------------------------------------------
        */

        MarketIndex::query()->delete();

        $markets = [
            [
                'name' => 'NGX All-Share',
                'symbol' => 'NGXASI',
                'value' => 102026.75,
                'change_value' => null,
                'change_percent' => 0.63,
                'as_of' => now(),
                'is_active' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'NGX 30',
                'symbol' => 'NGX30',
                'value' => 3938.85,
                'change_value' => null,
                'change_percent' => 0.41,
                'as_of' => now(),
                'is_active' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'Banking',
                'symbol' => 'BANKING',
                'value' => 1205.67,
                'change_value' => null,
                'change_percent' => 0.75,
                'as_of' => now(),
                'is_active' => true,
                'sort_order' => 3,
            ],

            [
                'name' => 'Industrial Goods',
                'symbol' => 'INDUSTRIAL',
                'value' => 4182.11,
                'change_value' => null,
                'change_percent' => -0.28,
                'as_of' => now(),
                'is_active' => true,
                'sort_order' => 4,
            ],

            [
                'name' => 'Oil & Gas',
                'symbol' => 'OILGAS',
                'value' => 2216.36,
                'change_value' => null,
                'change_percent' => 0.31,
                'as_of' => now(),
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($markets as $market) {
            MarketIndex::create($market);
        }
    }
}