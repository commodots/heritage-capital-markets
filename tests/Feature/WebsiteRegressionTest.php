<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WebsiteRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_homepage_copy_changes_are_visible_on_the_public_homepage(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/homepage/settings', [
                'homepage_about_title' => 'Built around our clients',
                'homepage_about_text_1' => 'Updated homepage copy.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page
                ->where('settings.homepage_about_title', 'Built around our clients')
                ->where('settings.homepage_about_text_1', 'Updated homepage copy.'));
    }

    public function test_about_and_investment_solutions_copy_changes_render_on_public_pages(): void
    {
        Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'is_published' => true,
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => AdminRole::query()->where('name', 'Legacy Administrator')->value('id'),
        ]);

        $this->actingAs($admin)
            ->post('/admin/page-content/about', [
                'about_hero_title_line_1' => 'Trusted capital markets expertise',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->post('/admin/page-content/investment-solutions', [
                'solutions_listing_hero_description' => 'Solutions aligned with each client’s goals.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->get('/about-us')
            ->assertInertia(fn (Assert $page) => $page
                ->where('content.about_hero_title_line_1', 'Trusted capital markets expertise'));

        $this->get('/investment-solutions')
            ->assertInertia(fn (Assert $page) => $page
                ->where('content.solutions_listing_hero_description', 'Solutions aligned with each client’s goals.'));
    }

    public function test_market_ticker_prop_is_shared_on_public_pages_without_fabricated_quotes(): void
    {
        Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'is_published' => true,
        ]);

        foreach ([
            '/',
            '/about-us',
            '/board-management',
            '/corporate-governance',
            '/fees-charges',
            '/privacy-policy',
            '/terms-of-use',
            '/risk-disclosure',
            '/investment-solutions',
            '/contact-us',
            '/resources-news',
            '/research-centre',
            '/resources/faqs',
            '/resources/downloads',
            '/resources',
            '/open-account',
            '/open-account/individual',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->has('marketTicker', 0));
        }
    }
}
