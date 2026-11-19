<?php

namespace Tests\Feature;

use App\Models\InvestmentSolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvestmentSolutionOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_and_publish_a_solution_specific_overview(): void
    {
        $solution = InvestmentSolution::create([
            'title' => 'Portfolio Management',
            'slug' => 'portfolio-management',
            'short_description' => 'Managed portfolios.',
            'description' => 'Professional portfolio management.',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get('/admin/investment-solutions')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/InvestmentSolutions/Index')
                ->where('solutions.0.overview_title', null));

        $this->actingAs($superAdmin)
            ->get('/admin/page-content/investment-details')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->missing('group.fields.solution_detail_overview_eyebrow')
                ->missing('group.fields.solution_detail_overview_title'));

        $this->actingAs($superAdmin)
            ->post("/admin/investment-solutions/{$solution->id}", [
                'title' => $solution->title,
                'slug' => $solution->slug,
                'short_description' => $solution->short_description,
                'description' => $solution->description,
                'overview_eyebrow' => 'Portfolio overview',
                'overview_title' => 'Investments shaped around your plan',
                'button_label' => 'Learn More',
                'button_url' => '/investment-solutions/portfolio-management',
                'is_active' => true,
                'sort_order' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('investment_solutions', [
            'id' => $solution->id,
            'overview_eyebrow' => 'Portfolio overview',
            'overview_title' => 'Investments shaped around your plan',
        ]);

        $this->get('/investment-solutions/portfolio-management')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('InvestmentSolution')
                ->where('solution.overview_eyebrow', 'Portfolio overview')
                ->where('solution.overview_title', 'Investments shaped around your plan')
                ->where('solution.description', 'Professional portfolio management.'));
    }
}
