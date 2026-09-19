<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Event;
use App\Models\Faq;
use App\Models\InvestmentSolution;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class HeritageContentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PUBLIC PAGES
        |--------------------------------------------------------------------------
        */

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'eyebrow' => 'About Heritage',
                'excerpt' => 'Building lasting value through professional investment and capital market solutions.',
                'content' => '<p>Heritage Capital Markets Limited is a Nigerian investment and capital market institution providing professional financial services to individuals, businesses and institutions.</p>',
                'featured_image' => 'images/about/heritage-building.jpg',
                'meta_title' => 'About Us | Heritage Capital Markets',
                'meta_description' => 'Learn about Heritage Capital Markets Limited, our values, expertise and commitment to creating lasting value.',
                'is_published' => true,
                'sort_order' => 1,
            ],

            [
                'title' => 'Investment Solutions',
                'slug' => 'investment-solutions',
                'eyebrow' => 'Our Services',
                'excerpt' => 'Investment solutions designed around your goals, risk profile and financial future.',
                'content' => '<p>Explore Heritage Capital Markets investment and financial solutions designed to help clients achieve their financial objectives.</p>',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'meta_title' => 'Investment Solutions | Heritage Capital Markets',
                'meta_description' => 'Explore investment and wealth management solutions from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 2,
            ],

            [
                'title' => 'Portfolio Management',
                'slug' => 'portfolio-management',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Professional management of your investments to maximize returns while managing risk.',
                'content' => '<p>Our portfolio management service provides customized investment strategies designed around your financial goals, risk appetite and investment horizon.</p>',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'meta_title' => 'Portfolio Management | Heritage Capital Markets',
                'meta_description' => 'Professional portfolio management services from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 3,
            ],

            [
                'title' => 'Investment Advisory',
                'slug' => 'investment-advisory',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Expert advice tailored to your financial goals and risk appetite.',
                'content' => '<p>Our investment advisory services provide professional guidance supported by market research, analysis and an understanding of your investment objectives.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'meta_title' => 'Investment Advisory | Heritage Capital Markets',
                'meta_description' => 'Investment advisory services tailored to your financial objectives.',
                'is_published' => true,
                'sort_order' => 4,
            ],

            [
                'title' => 'Brokerage & Dealing Services',
                'slug' => 'brokerage-dealing',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Seamless execution of trades across equity, fixed income and other securities.',
                'content' => '<p>Our brokerage and dealing services provide clients with professional market access and transaction execution across relevant capital market instruments.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'meta_title' => 'Brokerage & Dealing Services | Heritage Capital Markets',
                'meta_description' => 'Professional brokerage and dealing services from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 5,
            ],

            [
                'title' => 'Fixed Income Investments',
                'slug' => 'fixed-income-investments',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Access a range of fixed income investment opportunities with attractive yield potential.',
                'content' => '<p>We provide access to relevant fixed income investment opportunities while considering investment objectives, tenure and prevailing market conditions.</p>',
                'featured_image' => 'images/hero/slide-04-financial-future.webp',
                'meta_title' => 'Fixed Income Investments | Heritage Capital Markets',
                'meta_description' => 'Explore fixed income investment opportunities through Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 6,
            ],

            [
                'title' => 'Alternative Investments',
                'slug' => 'alternative-investments',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Diversify your portfolio with carefully selected alternative asset classes.',
                'content' => '<p>Our alternative investment offering provides access to opportunities beyond traditional asset classes for suitable investors.</p>',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'meta_title' => 'Alternative Investments | Heritage Capital Markets',
                'meta_description' => 'Alternative investment opportunities from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 7,
            ],

            [
                'title' => 'Financial Planning',
                'slug' => 'financial-planning',
                'eyebrow' => 'Investment Solutions',
                'excerpt' => 'Plan for your future with comprehensive financial planning and wealth management services.',
                'content' => '<p>Our financial planning approach brings together your financial objectives, investment priorities and longer-term plans.</p>',
                'featured_image' => 'images/about/heritage-reception.jpg',
                'meta_title' => 'Financial Planning | Heritage Capital Markets',
                'meta_description' => 'Financial planning and wealth management services from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 8,
            ],

            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'eyebrow' => 'Get In Touch',
                'excerpt' => 'Connect with our team for investment and capital market enquiries.',
                'content' => '<p>Contact Heritage Capital Markets to discuss your investment, financial planning or capital market requirements.</p>',
                'featured_image' => 'images/about/heritage-building.jpg',
                'meta_title' => 'Contact Us | Heritage Capital Markets',
                'meta_description' => 'Contact Heritage Capital Markets for investment and capital market enquiries.',
                'is_published' => true,
                'sort_order' => 9,
            ],

            [
                'title' => 'Research Centre',
                'slug' => 'research-centre',
                'eyebrow' => 'Research & Insights',
                'excerpt' => 'Access market insights, research reports and financial perspectives.',
                'content' => '<p>Explore research and market insights from Heritage Capital Markets.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'meta_title' => 'Research Centre | Heritage Capital Markets',
                'meta_description' => 'Market research, reports and insights from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 10,
            ],

            [
                'title' => 'Resources & News',
                'slug' => 'resources-news',
                'eyebrow' => 'Resources',
                'excerpt' => 'Stay informed with Heritage Capital Markets news, articles and resources.',
                'content' => '<p>Read the latest news, articles, market commentary and resources from Heritage Capital Markets.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'meta_title' => 'Resources & News | Heritage Capital Markets',
                'meta_description' => 'News, articles and resources from Heritage Capital Markets.',
                'is_published' => true,
                'sort_order' => 11,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HOMEPAGE SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings = [
            'homepage_why_eyebrow' => 'WHY HERITAGE',
            'homepage_why_title' => 'Built on Trust. Driven by Insight.',
            'homepage_why_description' => 'At Heritage Capital Markets, we combine market knowledge, professional expertise and a client-focused approach to help individuals and institutions make informed financial decisions.',

            'homepage_stat_1_value' => '30+',
            'homepage_stat_1_label' => 'Years Experience',

            'homepage_stat_2_value' => '100+',
            'homepage_stat_2_label' => 'Clients Served',

            'homepage_stat_3_value' => '₦850B+',
            'homepage_stat_3_label' => 'Assets Under Management',

            'homepage_stat_4_value' => '120+',
            'homepage_stat_4_label' => 'Dedicated Professionals',

            'homepage_about_eyebrow' => 'ABOUT HERITAGE',
            'homepage_about_title' => 'More Than a Firm. A Lasting Impact.',
            'homepage_about_text_1' => 'Heritage Capital Markets Limited provides professional investment and capital market services designed to help clients build, preserve and grow wealth.',
            'homepage_about_text_2' => 'Our approach combines experienced professionals, market intelligence and a strong commitment to client service.',
            'homepage_about_button' => 'Discover Our Story',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ARTICLES
        |--------------------------------------------------------------------------
        */

        $articles = [
            [
                'title' => 'Understanding Investment Opportunities in Nigeria',
                'slug' => 'understanding-investment-opportunities-in-nigeria',
                'category' => 'Investment Insights',
                'excerpt' => 'An overview of key investment considerations for investors navigating the Nigerian market.',
                'content' => '<p>Investment decisions should take into consideration objectives, risk appetite, investment horizon and prevailing market conditions.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'published_at' => now(),
                'is_published' => true,
                'sort_order' => 1,
            ],

            [
                'title' => 'Building a Long-Term Investment Strategy',
                'slug' => 'building-a-long-term-investment-strategy',
                'category' => 'Financial Planning',
                'excerpt' => 'Key considerations when developing a disciplined long-term investment strategy.',
                'content' => '<p>A long-term investment strategy should reflect clearly defined financial objectives and an appropriate approach to diversification and risk management.</p>',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'published_at' => now(),
                'is_published' => true,
                'sort_order' => 2,
            ],

            [
                'title' => 'Understanding Fixed Income Investments',
                'slug' => 'understanding-fixed-income-investments',
                'category' => 'Fixed Income',
                'excerpt' => 'An introduction to fixed income investments and the factors investors should consider.',
                'content' => '<p>Fixed income investments can play an important role in an investment portfolio. Investors should consider tenure, yield, liquidity and credit considerations.</p>',
                'featured_image' => 'images/hero/slide-04-financial-future.webp',
                'published_at' => now(),
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                $article
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FAQS
        |--------------------------------------------------------------------------
        */

        $faqs = [
            [
                'question' => 'What services does Heritage Capital Markets provide?',
                'answer' => 'Heritage Capital Markets provides investment and capital market services including portfolio management, investment advisory, brokerage and dealing, fixed income investments, alternative investments and financial planning.',
                'category' => 'General',
                'is_active' => true,
                'sort_order' => 1,
            ],

            [
                'question' => 'How can I contact Heritage Capital Markets?',
                'answer' => 'You can contact Heritage Capital Markets through the Contact Us page on this website or by using the available contact channels provided by the company.',
                'category' => 'General',
                'is_active' => true,
                'sort_order' => 2,
            ],

            [
                'question' => 'What is portfolio management?',
                'answer' => 'Portfolio management involves the professional management of investments based on a client’s objectives, risk profile and investment horizon.',
                'category' => 'Investment',
                'is_active' => true,
                'sort_order' => 3,
            ],

            [
                'question' => 'What are fixed income investments?',
                'answer' => 'Fixed income investments are investments that generally provide scheduled income or returns over a defined period, subject to the characteristics and risks of the particular instrument.',
                'category' => 'Investment',
                'is_active' => true,
                'sort_order' => 4,
            ],

            [
                'question' => 'Can Heritage help me develop a financial plan?',
                'answer' => 'Yes. Heritage provides financial planning services designed to help clients structure their financial objectives and investment priorities.',
                'category' => 'Financial Planning',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EVENTS
        |--------------------------------------------------------------------------
        */

        $events = [
            [
                'title' => 'Heritage Capital Markets Investor Forum',
                'slug' => 'heritage-capital-markets-investor-forum',
                'excerpt' => 'An investor-focused forum exploring market developments and investment opportunities.',
                'description' => '<p>Details of this event will be announced by Heritage Capital Markets.</p>',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'event_date' => now()->addMonths(1),
                'location' => 'Lagos, Nigeria',
                'is_published' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['slug' => $event['slug']],
                $event
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INVESTMENT SOLUTIONS
        |--------------------------------------------------------------------------
        */

        $investmentSolutions = [
            [
                'slug' => 'portfolio-management',
                'title' => 'Portfolio Management',
                'short_description' => 'Professional management of your investments to maximize returns while managing risk.',
                'description' => 'Our portfolio management service provides customized investment strategies designed around your financial goals, risk appetite and investment horizon.',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'button_label' => 'Learn More',
                'button_url' => '/portfolio-management',
                'is_active' => true,
                'sort_order' => 1,
            ],

            [
                'slug' => 'investment-advisory',
                'title' => 'Investment Advisory',
                'short_description' => 'Expert advice tailored to your financial goals and risk appetite.',
                'description' => 'Our investment advisory services provide professional guidance supported by market research, analysis and an understanding of your investment objectives.',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'button_label' => 'Learn More',
                'button_url' => '/investment-advisory',
                'is_active' => true,
                'sort_order' => 2,
            ],

            [
                'slug' => 'brokerage-dealing',
                'title' => 'Brokerage & Dealing Services',
                'short_description' => 'Seamless execution of trades across equity, fixed income and other securities.',
                'description' => 'Our brokerage and dealing services provide clients with professional market access and transaction execution across relevant capital market instruments.',
                'featured_image' => 'images/hero/slide-02-market-insights.webp',
                'button_label' => 'Learn More',
                'button_url' => '/brokerage-dealing',
                'is_active' => true,
                'sort_order' => 3,
            ],

            [
                'slug' => 'fixed-income-investments',
                'title' => 'Fixed Income Investments',
                'short_description' => 'Access a wide range of fixed income instruments with attractive yield opportunities.',
                'description' => 'We provide access to relevant fixed income investment opportunities while considering investment objectives, tenure and prevailing market conditions.',
                'featured_image' => 'images/hero/slide-04-financial-future.webp',
                'button_label' => 'Learn More',
                'button_url' => '/fixed-income-investments',
                'is_active' => true,
                'sort_order' => 4,
            ],

            [
                'slug' => 'alternative-investments',
                'title' => 'Alternative Investments',
                'short_description' => 'Diversify your portfolio with carefully selected alternative asset classes.',
                'description' => 'Our alternative investment offering provides access to opportunities beyond traditional asset classes for suitable investors.',
                'featured_image' => 'images/hero/slide-01-coins-tree.webp',
                'button_label' => 'Learn More',
                'button_url' => '/alternative-investments',
                'is_active' => true,
                'sort_order' => 5,
            ],

            [
                'slug' => 'financial-planning',
                'title' => 'Financial Planning',
                'short_description' => 'Plan for your future with comprehensive financial planning and wealth management services.',
                'description' => 'Our financial planning approach brings together your financial objectives, investment priorities and longer-term plans.',
                'featured_image' => 'images/about/heritage-reception.jpg',
                'button_label' => 'Learn More',
                'button_url' => '/financial-planning',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($investmentSolutions as $solution) {
            InvestmentSolution::updateOrCreate(
                ['slug' => $solution['slug']],
                $solution
            );
        }
    }
}