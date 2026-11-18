<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccountApplicationController;
use App\Http\Controllers\Admin\AccountApplicationAdminController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\HomepageSettingsController;
use App\Http\Controllers\Admin\InvestmentSolutionController as AdminInvestmentSolutionController;
use App\Http\Controllers\Admin\LiveChatController;
use App\Http\Controllers\Admin\MarketIndexController;
use App\Http\Controllers\Admin\MarketQuoteController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PublicPageContentController;
use App\Http\Controllers\Admin\ResearchReportController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadsController;
use App\Http\Controllers\FaqController as PublicFaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvestmentSolutionController;
use App\Http\Controllers\InvestmentSolutionsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OpenAccountController;
use App\Http\Controllers\PublicContentPageController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicResourcesController;
use App\Http\Controllers\ResearchCentreController;
use App\Http\Controllers\ResourcesNewsController;
use App\Http\Controllers\SearchController;
use App\Support\AccountOpeningFormSchema;
use App\Support\PublicPageContent;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
    |--------------------------------------------------------------------------
    |Authentication
    |--------------------------------------------------------------------------
    */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('login', [LoginController::class, 'store'])
        ->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/about-us', [AboutController::class, 'index'])
    ->name('about');

Route::get('/board-management', [PublicContentPageController::class, 'boardManagement'])
    ->name('board-management');

Route::get('/corporate-governance', [PublicContentPageController::class, 'corporateGovernance'])
    ->name('corporate-governance');

Route::get('/fees-charges', [PublicContentPageController::class, 'feesCharges'])
    ->name('fees-charges');

Route::get('/privacy-policy', [PublicContentPageController::class, 'privacyPolicy'])
    ->name('privacy-policy');

Route::get('/terms-of-use', [PublicContentPageController::class, 'termsOfUse'])
    ->name('terms-of-use');

Route::get('/risk-disclosure', [PublicContentPageController::class, 'riskDisclosure'])
    ->name('risk-disclosure');

Route::get('/investor-login', fn () => Inertia::render('InvestorLogin', [
    'content' => PublicPageContent::groupValues('investor-login'),
]))
    ->name('investor-login');

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

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe/{newsletterSubscriber}', [NewsletterController::class, 'confirmUnsubscribe'])
    ->middleware('signed')
    ->name('newsletter.unsubscribe.confirm');

Route::post('/newsletter/unsubscribe/{newsletterSubscriber}', [NewsletterController::class, 'unsubscribe'])
    ->middleware('signed')
    ->name('newsletter.unsubscribe.process');

Route::prefix('chat')->group(function () {
    Route::get('/availability', [ChatController::class, 'availability'])->middleware('throttle:60,1');
    Route::post('/start', [ChatController::class, 'start'])->middleware('throttle:5,1');
    Route::post('/message', [ChatController::class, 'message'])->middleware('throttle:20,1');
    Route::get('/messages', [ChatController::class, 'messages'])->middleware('throttle:60,1');
    Route::post('/offline-enquiry', [ChatController::class, 'offlineEnquiry'])->middleware('throttle:5,1');
});

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

Route::redirect('/research-centre/market-overview', '/#market-overview');
Route::redirect('/research-centre/reports', '/research-centre');

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

Route::get('/resources/downloads/account-opening/{type}.pdf', [AccountApplicationController::class, 'downloadBlankForm'])
    ->whereIn('type', AccountOpeningFormSchema::TYPES)
    ->name('resources.downloads.account-opening');

Route::get('/resources/downloads/{media}/file', [DownloadsController::class, 'download'])
    ->whereNumber('media')
    ->name('resources.downloads.file');

Route::get('/resources', [PublicResourcesController::class, 'index'])
    ->name('resources');

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
| Account Opening Applications
|--------------------------------------------------------------------------
*/

Route::get('/open-account/{type}', [AccountApplicationController::class, 'start'])
    ->whereIn('type', ['individual', 'corporate', 'joint', 'minor', 'estate'])
    ->name('account-application.start');

Route::post('/open-account/{type}/save-step', [AccountApplicationController::class, 'saveStep'])
    ->whereIn('type', ['individual', 'corporate', 'joint', 'minor', 'estate'])
    ->middleware('throttle:20,1')
    ->name('account-application.save-step');

Route::post('/open-account/{type}/submit', [AccountApplicationController::class, 'submit'])
    ->whereIn('type', ['individual', 'corporate', 'joint', 'minor', 'estate'])
    ->middleware('throttle:5,1')
    ->name('account-application.submit');

Route::get('/open-account/{type}/submitted', [AccountApplicationController::class, 'success'])
    ->name('open-account.success');
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

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::middleware('feature:live_chat')->prefix('live-chat')->group(function () {
            Route::get('/unread-count', [LiveChatController::class, 'unreadCount'])
                ->name('admin.live-chat.unread-count');
            Route::get('/', [LiveChatController::class, 'index'])
                ->name('admin.live-chat');
            Route::get('/{conversation}', [LiveChatController::class, 'show'])
                ->name('admin.live-chat.show');
            Route::get('/{conversation}/poll', [LiveChatController::class, 'poll'])
                ->name('admin.live-chat.poll');
            Route::post('/{conversation}/reply', [LiveChatController::class, 'reply'])
                ->name('admin.live-chat.reply');
            Route::post('/{conversation}/assign', [LiveChatController::class, 'assign'])
                ->name('admin.live-chat.assign');
            Route::post('/{conversation}/close', [LiveChatController::class, 'close'])
                ->name('admin.live-chat.close');
        });

        /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('feature:dashboard')
            ->name('admin.dashboard');

        /*
    |--------------------------------------------------------------------------
    | Homepage Management
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:homepage')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:pages')->group(function () {
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
        });

        Route::get('/page-content/board', [PublicPageContentController::class, 'edit'])
            ->defaults('group', 'board')
            ->middleware('feature:board_management')
            ->name('admin.page-content.board.edit');

        Route::post('/page-content/board', [PublicPageContentController::class, 'update'])
            ->defaults('group', 'board')
            ->middleware('feature:board_management')
            ->name('admin.page-content.board.update');

        Route::middleware('feature:pages')->group(function () {
            Route::get('/page-content/{group}', [PublicPageContentController::class, 'edit'])
                ->whereIn('group', ['about', 'investment-solutions', 'investment-details', 'investor-login', 'governance', 'fees', 'resources', 'privacy', 'terms', 'risk'])
                ->name('admin.page-content.edit');

            Route::post('/page-content/{group}', [PublicPageContentController::class, 'update'])
                ->whereIn('group', ['about', 'investment-solutions', 'investment-details', 'investor-login', 'governance', 'fees', 'resources', 'privacy', 'terms', 'risk'])
                ->name('admin.page-content.update');
        });

        /*
    |--------------------------------------------------------------------------
    | Investment Solutions
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:investment_solutions')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | Market Indices
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:market_indices')->group(function () {
            Route::get('/market-indices', [MarketIndexController::class, 'index'])
                ->name('admin.market-indices');

            Route::post('/market-indices', [MarketIndexController::class, 'store'])
                ->name('admin.market-indices.store');

            Route::post('/market-indices/import-csv', [MarketIndexController::class, 'previewCsv'])
                ->name('admin.market-indices.import-csv');

            Route::post('/market-indices/import-csv/commit', [MarketIndexController::class, 'commitCsv'])
                ->name('admin.market-indices.import-csv.commit');

            Route::post('/market-indices/import-csv/cancel', [MarketIndexController::class, 'cancelCsv'])
                ->name('admin.market-indices.import-csv.cancel');

            Route::post('/market-indices/import-api', [MarketIndexController::class, 'importApi'])
                ->name('admin.market-indices.import-api');

            Route::post('/market-indices/{marketIndex}', [MarketIndexController::class, 'update'])
                ->name('admin.market-indices.update');

            Route::post('/market-indices/{marketIndex}/toggle', [MarketIndexController::class, 'toggle'])
                ->name('admin.market-indices.toggle');

            Route::delete('/market-indices/{marketIndex}', [MarketIndexController::class, 'destroy'])
                ->name('admin.market-indices.destroy');

            Route::get('/market-quotes', [MarketQuoteController::class, 'index'])
                ->name('admin.market-quotes');

            Route::post('/market-quotes', [MarketQuoteController::class, 'store'])
                ->name('admin.market-quotes.store');

            Route::post('/market-quotes/import-csv', [MarketQuoteController::class, 'previewCsv'])
                ->name('admin.market-quotes.import-csv');

            Route::post('/market-quotes/import-csv/commit', [MarketQuoteController::class, 'commitCsv'])
                ->name('admin.market-quotes.import-csv.commit');

            Route::post('/market-quotes/import-csv/cancel', [MarketQuoteController::class, 'cancelCsv'])
                ->name('admin.market-quotes.import-csv.cancel');

            Route::post('/market-quotes/{marketQuote}', [MarketQuoteController::class, 'update'])
                ->name('admin.market-quotes.update');

            Route::post('/market-quotes/{marketQuote}/toggle', [MarketQuoteController::class, 'toggle'])
                ->name('admin.market-quotes.toggle');

            Route::delete('/market-quotes/{marketQuote}', [MarketQuoteController::class, 'destroy'])
                ->name('admin.market-quotes.destroy');
        });

        /*
    |--------------------------------------------------------------------------
    | Research Reports
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:research')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | Articles / News
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:articles')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:events')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | FAQs
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:faqs')->group(function () {
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
        });

        /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:media')->group(function () {
            Route::get('/media', [MediaController::class, 'index'])
                ->name('admin.media');

            Route::post('/media', [MediaController::class, 'store'])
                ->name('admin.media.store');

            Route::post('/media/documents', [MediaController::class, 'storeDocument'])
                ->middleware('superadmin')
                ->name('admin.media.documents.store');

            Route::delete('/media/{media}', [MediaController::class, 'destroy'])
                ->name('admin.media.destroy');
        });

        /*
    |--------------------------------------------------------------------------
    | Newsletter Subscribers
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:newsletter')->group(function () {
            Route::get('/newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])
                ->name('admin.newsletter-subscribers');

            Route::post('/newsletter-subscribers/campaign', [NewsletterSubscriberController::class, 'sendCampaign'])
                ->name('admin.newsletter-subscribers.campaign');

            Route::post('/newsletter-subscribers/{newsletterSubscriber}/toggle', [NewsletterSubscriberController::class, 'toggle'])
                ->name('admin.newsletter-subscribers.toggle');

            Route::get('/newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])
                ->name('admin.newsletter-subscribers.export');
        });

        /*
    |--------------------------------------------------------------------------
    | Site Settings
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:settings')->group(function () {
            Route::get('/settings', [SiteSettingController::class, 'index'])
                ->name('admin.settings');

            Route::post('/settings', [SiteSettingController::class, 'update'])
                ->name('admin.settings.update');
        });

        /*
    |--------------------------------------------------------------------------
    | Enquiries
    |--------------------------------------------------------------------------
    */

        Route::middleware('feature:enquiries')->group(function () {
            Route::get('/enquiries', [EnquiryController::class, 'index'])
                ->name('admin.enquiries');

            Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])
                ->name('admin.enquiries.show');

            Route::post('/enquiries/{enquiry}', [EnquiryController::class, 'update'])
                ->name('admin.enquiries.update');
        });

        /*
    |--------------------------------------------------------------------------
    | Newsletter
    |--------------------------------------------------------------------------
    */
        Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
            ->middleware('feature:newsletter')
            ->name('newsletter.subscribe');

        /*
        |--------------------------------------------------------------------------
        | Account Applications
        |--------------------------------------------------------------------------
        */

        Route::middleware('feature:account_applications')->group(function () {
            Route::get('/account-applications', [AccountApplicationAdminController::class, 'index'])
                ->name('admin.account-applications');

            Route::get('/account-applications/{application}', [AccountApplicationAdminController::class, 'show'])
                ->name('admin.account-applications.show');

            Route::post('/account-applications/{application}', [AccountApplicationAdminController::class, 'update'])
                ->name('admin.account-applications.update');

            Route::get('/account-applications/{application}/pdf', [AccountApplicationController::class, 'downloadPdf'])
                ->middleware('superadmin')
                ->name('admin.account-applications.pdf');

            Route::get('/account-applications/{application}/documents/{document}', [AccountApplicationAdminController::class, 'download'])
                ->name('admin.account-applications.documents.download');
        });

        Route::middleware(['feature:user_management', 'superadmin'])->group(function () {
            Route::get('/user-types', [AdminRoleController::class, 'index'])
                ->name('admin.user-types');
            Route::post('/user-types', [AdminRoleController::class, 'store'])
                ->name('admin.user-types.store');
            Route::put('/user-types/{adminRole}', [AdminRoleController::class, 'update'])
                ->name('admin.user-types.update');

            Route::get('/users', [AdminUserController::class, 'index'])
                ->name('admin.users');
            Route::post('/users', [AdminUserController::class, 'store'])
                ->name('admin.users.store');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])
                ->name('admin.users.update');
            Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggle'])
                ->name('admin.users.toggle');
        });
    });

Route::get('/{slug}', [PublicPageController::class, 'show'])
    ->name('public-page.show');
