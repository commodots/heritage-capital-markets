<?php

namespace Tests\Feature;

use App\Models\MarketIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketIndexImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_market_indices_can_be_created_manually_with_the_correct_signed_change(): void
    {
        $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]))->post('/admin/market-indices', [
            'name' => 'NGX All-Share Index',
            'symbol' => 'NGXASI',
            'value' => '102026.75',
            'change' => '640.25',
            'change_percent' => '0.63',
            'is_positive' => '0',
            'is_active' => '1',
            'sort_order' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGXASI',
            'change_value' => '-640.25',
            'change_percent' => '-0.63',
        ]);
    }

    public function test_csv_import_creates_and_updates_indices_by_symbol(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true]);
        $existing = MarketIndex::create([
            'name' => 'Old Index Name',
            'symbol' => 'NGXASI',
            'value' => 100,
            'change_value' => 2,
            'change_percent' => 2,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $csv = UploadedFile::fake()->createWithContent(
            'indices.csv',
            "name,symbol,value,change,change_percent,is_positive,is_active,sort_order\n".
            "NGX All-Share Index,NGXASI,102026.75,640.25,0.63,1,1,1\n".
            "NGX Banking Index,NGXBNK,1450.10,-4.50,-0.31,0,1,2\n",
        );

        $this->actingAs($admin)->post('/admin/market-indices/import-csv', [
            'csv_file' => $csv,
        ])->assertRedirect()->assertSessionHasNoErrors()
            ->assertSessionHas('marketIndexCsvPreview');

        $preview = $this->app['session.store']->get('marketIndexCsvPreview');
        $this->actingAs($admin)->post('/admin/market-indices/import-csv/commit', [
            'preview_token' => $preview['token'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseCount('market_indices', 2);
        $this->assertDatabaseHas('market_indices', [
            'id' => $existing->id,
            'name' => 'NGX All-Share Index',
            'value' => '102026.7500',
            'change_value' => '640.2500',
        ]);
        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGXBNK',
            'change_value' => '-4.5000',
            'change_percent' => '-0.3100',
        ]);
    }

    public function test_index_csv_preview_reports_invalid_and_duplicate_rows_and_commits_only_valid_rows(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true]);
        MarketIndex::create([
            'name' => 'NGX All-Share',
            'symbol' => 'NGXASI',
            'value' => 100,
            'change_value' => 1,
            'change_percent' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $csv = UploadedFile::fake()->createWithContent(
            'indices.csv',
            "name,symbol,value,change,change_percent\n".
            "NGX All-Share,NGXASI,105,5,5\n".
            "New Banking Index,NGXBNK,200,2,1\n".
            "New Banking Index,NGXBNK,201,3,2\n".
            "Invalid Index,NGXBAD,not-a-price,1,1\n",
        );

        $this->actingAs($admin)->post('/admin/market-indices/import-csv', [
            'csv_file' => $csv,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $preview = $this->app['session.store']->get('marketIndexCsvPreview');
        $this->assertTrue($preview['rows'][0]['valid']);
        $this->assertFalse($preview['rows'][1]['valid']);
        $this->assertFalse($preview['rows'][2]['valid']);
        $this->assertFalse($preview['rows'][3]['valid']);
        $this->assertDatabaseCount('market_indices', 1);

        $this->actingAs($admin)->post('/admin/market-indices/import-csv/commit', [
            'preview_token' => $preview['token'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $report = $this->app['session.store']->get('marketIndexCsvReport');
        $this->assertSame(1, $report['succeeded']);
        $this->assertSame(0, $report['failed']);
        $this->assertSame(3, $report['invalid']);
        $this->assertDatabaseCount('market_indices', 1);
        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGXASI',
            'value' => '105.0000',
        ]);
    }

    public function test_configured_api_prices_can_be_imported(): void
    {
        config([
            'services.market_indices.approved' => true,
            'services.market_indices.url' => 'https://prices.example.test/api/indices',
            'services.market_indices.token' => 'api-token',
        ]);
        Http::fake([
            'prices.example.test/api/indices' => Http::response([
                'data' => [[
                    'name' => 'NGX Banking Index',
                    'symbol' => 'NGXBNK',
                    'value' => 1450.10,
                    'change' => 4.50,
                    'change_percent' => 0.31,
                    'is_positive' => false,
                ]],
            ]),
        ]);

        $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]))->post('/admin/market-indices/import-api')
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer api-token'));
        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGXBNK',
            'change_value' => '-4.5',
            'change_percent' => '-0.31',
        ]);
    }

    public function test_market_index_api_import_requires_hcml_approval(): void
    {
        config([
            'services.market_indices.approved' => false,
            'services.market_indices.url' => 'https://prices.example.test/api/indices',
        ]);

        $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]))->post('/admin/market-indices/import-api')
            ->assertRedirect()
            ->assertSessionHasErrors('api');

        $this->assertDatabaseCount('market_indices', 0);
    }

    public function test_seeded_demo_prices_are_hidden_and_cleared_without_affecting_real_data(): void
    {
        MarketIndex::create([
            'name' => 'NGX All-Share',
            'symbol' => 'NGXASI',
            'value' => 102026.75,
            'change_value' => null,
            'change_percent' => 0.63,
            'as_of' => now(),
            'is_active' => true,
            'sort_order' => 1,
        ]);
        MarketIndex::create([
            'name' => 'NGX 30',
            'symbol' => 'NGX30',
            'value' => 4000,
            'change_value' => 2,
            'change_percent' => 0.05,
            'as_of' => now(),
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $migration = require database_path('migrations/2026_11_18_122000_hide_seeded_demo_market_indices.php');
        $migration->up();

        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGXASI',
            'value' => null,
            'change_value' => null,
            'change_percent' => null,
            'as_of' => null,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('market_indices', [
            'symbol' => 'NGX30',
            'value' => '4000.0000',
            'is_active' => true,
        ]);
    }
}
