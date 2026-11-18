<?php

namespace Tests\Feature;

use App\Models\MarketIndex;
use App\Models\MarketQuote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketQuoteImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);
    }

    public function test_admin_can_create_a_manual_stock_quote(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/market-quotes')->assertOk();

        $this->actingAs($admin)->post('/admin/market-quotes', [
            'symbol' => 'dangcem',
            'name' => 'Dangote Cement Plc',
            'value' => '700.5',
            'change' => '-2.5',
            'change_percent' => '-0.35',
            'is_active' => true,
            'sort_order' => 1,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('market_quotes', [
            'symbol' => 'DANGCEM',
            'name' => 'Dangote Cement Plc',
            'value' => '700.5000',
            'change_value' => '-2.5000',
            'change_percent' => '-0.3500',
        ]);

        $this->actingAs($admin)
            ->from('/admin/market-quotes')
            ->post('/admin/market-quotes', [
                'symbol' => 'DANGCEM',
                'name' => 'Duplicate Dangote Cement',
                'value' => '700.5',
                'change' => '-2.5',
                'change_percent' => '-0.35',
                'is_active' => true,
                'sort_order' => 2,
            ])
            ->assertRedirect('/admin/market-quotes')
            ->assertSessionHasErrors('symbol');

        $this->assertDatabaseCount('market_quotes', 1);
    }

    public function test_quote_csv_preview_reports_invalid_duplicates_and_unknown_symbols_before_commit(): void
    {
        $admin = $this->admin();
        $quote = MarketQuote::create([
            'symbol' => 'DANGCEM',
            'name' => 'Old Company Name',
            'value' => 100,
            'change_value' => 1,
            'change_percent' => 1,
            'as_of' => now()->subDay(),
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $csv = UploadedFile::fake()->createWithContent(
            'quotes.csv',
            "symbol,name,value,change,change_percent,as_of\n".
            "DANGCEM,Dangote Cement Plc,700.5,-2.5,-0.35,2026-11-18T12:00:00Z\n".
            "GTCO,Guaranty Trust Holding Plc,100,1,1,\n".
            "GTCO,Guaranty Trust Holding Plc,101,2,2,\n".
            "MISSING,Unknown Plc,10,1,1,\n".
            "BAD,Invalid Plc,not-a-price,1,1,\n",
        );

        $this->actingAs($admin)->post('/admin/market-quotes/import-csv', [
            'csv_file' => $csv,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $preview = $this->app['session.store']->get('marketQuoteCsvPreview');
        $this->assertTrue($preview['rows'][0]['valid']);
        $this->assertFalse($preview['rows'][1]['valid']);
        $this->assertFalse($preview['rows'][2]['valid']);
        $this->assertFalse($preview['rows'][3]['valid']);
        $this->assertFalse($preview['rows'][4]['valid']);
        $this->assertDatabaseCount('market_quotes', 1);
        $this->assertSame('Old Company Name', $quote->fresh()->name);

        $this->actingAs($admin)->post('/admin/market-quotes/import-csv/commit', [
            'preview_token' => $preview['token'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $report = $this->app['session.store']->get('marketQuoteCsvReport');
        $this->assertSame(1, $report['succeeded']);
        $this->assertSame(0, $report['failed']);
        $this->assertSame(4, $report['invalid']);
        $this->assertDatabaseCount('market_quotes', 1);
        $this->assertDatabaseHas('market_quotes', [
            'id' => $quote->id,
            'name' => 'Dangote Cement Plc',
            'value' => '700.5000',
            'change_value' => '-2.5000',
            'change_percent' => '-0.3500',
        ]);
    }

    public function test_public_ticker_shares_only_persisted_quote_and_index_values(): void
    {
        MarketQuote::create([
            'symbol' => 'DANGCEM',
            'name' => 'Dangote Cement Plc',
            'value' => 700.5,
            'change_value' => -2.5,
            'change_percent' => -0.35,
            'as_of' => now(),
            'is_active' => true,
            'sort_order' => 1,
        ]);
        MarketIndex::create([
            'name' => 'NGX All-Share',
            'symbol' => 'NGXASI',
            'value' => 100000,
            'change_value' => 100,
            'change_percent' => 0.1,
            'as_of' => now(),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get('/board-management')
            ->assertInertia(fn (Assert $page) => $page
                ->where('marketTicker.0.symbol', 'DANGCEM')
                ->where('marketTicker.0.name', 'Dangote Cement Plc')
                ->where('marketTicker.0.change_value', '-2.5000')
                ->where('marketTicker.0.change_percent', '-0.3500')
                ->has('marketTicker.0.as_of')
                ->where('marketTicker.1.symbol', 'NGXASI'));
    }

    public function test_public_ticker_has_no_fabricated_values_when_market_data_is_empty(): void
    {
        $this->get('/')
            ->assertInertia(fn (Assert $page) => $page->where('marketTicker', []));
    }
}
