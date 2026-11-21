<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_solutions', function (Blueprint $table) {
            $table->string('overview_eyebrow', 100)->nullable();
            $table->string('overview_title')->nullable();
        });

        $overviewSettings = Schema::hasTable('site_settings')
            ? DB::table('site_settings')
                ->whereIn('key', [
                    'solution_detail_overview_eyebrow',
                    'solution_detail_overview_title',
                ])
                ->pluck('value', 'key')
            : collect();

        DB::table('investment_solutions')->update([
            'overview_eyebrow' => $overviewSettings['solution_detail_overview_eyebrow'] ?? 'Overview',
            'overview_title' => $overviewSettings['solution_detail_overview_title'] ?? 'A solution designed around your objectives',
        ]);
    }

    public function down(): void
    {
        Schema::table('investment_solutions', function (Blueprint $table) {
            $table->dropColumn(['overview_eyebrow', 'overview_title']);
        });
    }
};
