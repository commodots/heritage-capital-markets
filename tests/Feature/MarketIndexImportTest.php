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

    public function test_configured_api_prices_can_be_imported(): void
    {
        config([
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
}
