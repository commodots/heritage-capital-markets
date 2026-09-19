<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ResourcesNewsController;
use App\Http\Controllers\ResearchCentreController;
use App\Http\Controllers\InvestmentSolutionsController;
use App\Http\Controllers\InvestmentSolutionController;
use App\Http\Controllers\FaqController as PublicFaqController;
use App\Http\Controllers\DownloadsController;
use App\Http\Controllers\OpenAccountController;
use App\Http\Controllers\SearchController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\InvestmentSolutionController as AdminInvestmentSolutionController;
use App\Http\Controllers\Admin\MarketIndexController;
use App\Http\Controllers\Admin\HomepageSettingsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ResearchReportController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\EnquiryController;


/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/about-us', [AboutController::class, 'index'])
    ->name('about');


/*
|--------------------------------------------------------------------------
| Investment Solutions
|--------------------------------------------------------------------------
*/

Route::get('/investment-solutions', [InvestmentSolutionsController::class, 'index'])
    ->name('investment-solutions');

Route::get('/investment-solutions/{slug}', [InvestmentSolutionController::class, 'show'])
    ->name('investment-solution.show');


/*
|--------------------------------------------------------------------------
| Contact
|--------------------------------------------------------------------------
*/

Route::get('/contact-us', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/contact-us', [ContactController::class, 'store'])
    ->name('contact.store');


/*
|--------------------------------------------------------------------------
| Resources & News
|--------------------------------------------------------------------------
*/

Route::get('/resources-news', [ResourcesNewsController::class, 'index'])
    ->name('resources-news');

Route::get('/resources-news/{slug}', [ResourcesNewsController::class, 'show'])
    ->name('resources-news.article');


/*
|--------------------------------------------------------------------------
| Research Centre
|--------------------------------------------------------------------------
*/

Route::get('/research-centre', [ResearchCentreController::class, 'index'])
    ->name('research-centre');

Route::get(
    '/research-centre/reports/{researchReport}',
    [ResearchCentreController::class, 'show']
)->name('research-centre.report');


/*
|--------------------------------------------------------------------------
| Public Resources
|--------------------------------------------------------------------------
*/

Route::get('/resources/faqs', [PublicFaqController::class, 'index'])
    ->name('resources.faqs');

Route::get('/resources/downloads', [DownloadsController::class, 'index'])
    ->name('resources.downloads');


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');


/*
|--------------------------------------------------------------------------
| Open Account
|--------------------------------------------------------------------------
*/

Route::get('/open-account', [OpenAccountController::class, 'index'])
    ->name('open-account');

Route::post('/open-account', [OpenAccountController::class, 'store'])
    ->name('open-account.store');


/*
|--------------------------------------------------------------------------
| Test 404
|--------------------------------------------------------------------------
*/

Route::get('/404', fn () => Inertia::render('Errors/404'))
    ->name('errors.404');


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/', [DashboardController::class, 'index'])
        ->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Homepage Management
    |--------------------------------------------------------------------------
    */

    Route::get('/homepage/hero-slides', [HeroSlideController::class, 'index'])
        ->name('admin.homepage.hero-slides');

    Route::post('/homepage/hero-slides', [HeroSlideController::class, 'store'])
        ->name('admin.homepage.hero-slides.store');

    Route::post('/homepage/hero-slides/{heroSlide}', [HeroSlideController::class, 'update'])
        ->name('admin.homepage.hero-slides.update');

    Route::post('/homepage/hero-slides/{heroSlide}/toggle', [HeroSlideController::class, 'toggle'])
        ->name('admin.homepage.hero-slides.toggle');

    Route::delete('/homepage/hero-slides/{heroSlide}', [HeroSlideController::class, 'destroy'])
        ->name('admin.homepage.hero-slides.destroy');


    /*
    |--------------------------------------------------------------------------
    | Homepage Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/homepage/settings', [HomepageSettingsController::class, 'index'])
        ->name('admin.homepage.settings');

    Route::post('/homepage/settings', [HomepageSettingsController::class, 'update'])
        ->name('admin.homepage.settings.update');


    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    Route::get('/pages', [PageController::class, 'index'])
        ->name('admin.pages');

    Route::post('/pages', [PageController::class, 'store'])
        ->name('admin.pages.store');

    Route::post('/pages/{page}', [PageController::class, 'update'])
        ->name('admin.pages.update');

    Route::post('/pages/{page}/toggle', [PageController::class, 'toggle'])
        ->name('admin.pages.toggle');

    Route::delete('/pages/{page}', [PageController::class, 'destroy'])
        ->name('admin.pages.destroy');


    /*
    |--------------------------------------------------------------------------
    | Investment Solutions
    |--------------------------------------------------------------------------
    */

    Route::get('/investment-solutions', [AdminInvestmentSolutionController::class, 'index'])
        ->name('admin.investment-solutions');

    Route::post('/investment-solutions', [AdminInvestmentSolutionController::class, 'store'])
        ->name('admin.investment-solutions.store');

    Route::post('/investment-solutions/{investmentSolution}', [AdminInvestmentSolutionController::class, 'update'])
        ->name('admin.investment-solutions.update');

    Route::post('/investment-solutions/{investmentSolution}/toggle', [AdminInvestmentSolutionController::class, 'toggle'])
        ->name('admin.investment-solutions.toggle');

    Route::delete('/investment-solutions/{investmentSolution}', [AdminInvestmentSolutionController::class, 'destroy'])
        ->name('admin.investment-solutions.destroy');


    /*
    |--------------------------------------------------------------------------
    | Market Indices
    |--------------------------------------------------------------------------
    */

    Route::get('/market-indices', [MarketIndexController::class, 'index'])
        ->name('admin.market-indices');

    Route::post('/market-indices', [MarketIndexController::class, 'store'])
        ->name('admin.market-indices.store');

    Route::post('/market-indices/{marketIndex}', [MarketIndexController::class, 'update'])
        ->name('admin.market-indices.update');

    Route::post('/market-indices/{marketIndex}/toggle', [MarketIndexController::class, 'toggle'])
        ->name('admin.market-indices.toggle');

    Route::delete('/market-indices/{marketIndex}', [MarketIndexController::class, 'destroy'])
        ->name('admin.market-indices.destroy');


    /*
    |--------------------------------------------------------------------------
    | Research Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/research-reports', [ResearchReportController::class, 'index'])
        ->name('admin.research-reports');

    Route::post('/research-reports', [ResearchReportController::class, 'store'])
        ->name('admin.research-reports.store');

    Route::post('/research-reports/{researchReport}', [ResearchReportController::class, 'update'])
        ->name('admin.research-reports.update');

    Route::post('/research-reports/{researchReport}/toggle', [ResearchReportController::class, 'toggle'])
        ->name('admin.research-reports.toggle');

    Route::delete('/research-reports/{researchReport}', [ResearchReportController::class, 'destroy'])
        ->name('admin.research-reports.destroy');


    /*
    |--------------------------------------------------------------------------
    | Articles / News
    |--------------------------------------------------------------------------
    */

    Route::get('/articles', [ArticleController::class, 'index'])
        ->name('admin.articles');

    Route::post('/articles', [ArticleController::class, 'store'])
        ->name('admin.articles.store');

    Route::post('/articles/{article}', [ArticleController::class, 'update'])
        ->name('admin.articles.update');

    Route::post('/articles/{article}/toggle', [ArticleController::class, 'toggle'])
        ->name('admin.articles.toggle');

    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])
        ->name('admin.articles.destroy');


    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    Route::get('/events', [EventController::class, 'index'])
        ->name('admin.events');

    Route::post('/events', [EventController::class, 'store'])
        ->name('admin.events.store');

    Route::post('/events/{event}', [EventController::class, 'update'])
        ->name('admin.events.update');

    Route::post('/events/{event}/toggle', [EventController::class, 'toggle'])
        ->name('admin.events.toggle');

    Route::delete('/events/{event}', [EventController::class, 'destroy'])
        ->name('admin.events.destroy');


    /*
    |--------------------------------------------------------------------------
    | FAQs
    |--------------------------------------------------------------------------
    */

    Route::get('/faqs', [FaqController::class, 'index'])
        ->name('admin.faqs');

    Route::post('/faqs', [FaqController::class, 'store'])
        ->name('admin.faqs.store');

    Route::post('/faqs/{faq}', [FaqController::class, 'update'])
        ->name('admin.faqs.update');

    Route::post('/faqs/{faq}/toggle', [FaqController::class, 'toggle'])
        ->name('admin.faqs.toggle');

    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])
        ->name('admin.faqs.destroy');


    /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    */

    Route::get('/media', [MediaController::class, 'index'])
        ->name('admin.media');

    Route::post('/media', [MediaController::class, 'store'])
        ->name('admin.media.store');

    Route::delete('/media/{media}', [MediaController::class, 'destroy'])
        ->name('admin.media.destroy');


    /*
    |--------------------------------------------------------------------------
    | Newsletter Subscribers
    |--------------------------------------------------------------------------
    */

    Route::get('/newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])
        ->name('admin.newsletter-subscribers');

    Route::post('/newsletter-subscribers/{newsletterSubscriber}/toggle', [NewsletterSubscriberController::class, 'toggle'])
        ->name('admin.newsletter-subscribers.toggle');

    Route::get('/newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])
        ->name('admin.newsletter-subscribers.export');


    /*
    |--------------------------------------------------------------------------
    | Site Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [SiteSettingController::class, 'index'])
        ->name('admin.settings');

    Route::post('/settings', [SiteSettingController::class, 'update'])
        ->name('admin.settings.update');


    /*
    |--------------------------------------------------------------------------
    | Enquiries
    |--------------------------------------------------------------------------
    */

    Route::get('/enquiries', [EnquiryController::class, 'index'])
        ->name('admin.enquiries');

    Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])
        ->name('admin.enquiries.show');

    Route::post('/enquiries/{enquiry}', [EnquiryController::class, 'update'])
        ->name('admin.enquiries.update');

});
