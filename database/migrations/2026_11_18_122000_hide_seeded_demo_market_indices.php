<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'NGXASI' => 102026.75,
            'NGX30' => 3938.85,
            'BANKING' => 1205.67,
            'INDUSTRIAL' => 4182.11,
            'OILGAS' => 2216.36,
        ] as $symbol => $value) {
            DB::table('market_indices')
                ->where('symbol', $symbol)
                ->where('value', $value)
                ->whereNull('change_value')
                ->update([
                    'value' => null,
                    'change_value' => null,
                    'change_percent' => null,
                    'as_of' => null,
                    'is_active' => false,
                ]);
        }
    }

    public function down(): void
    {
        // Keep demo prices hidden when rolling back; restoring them could publish fabricated data.
    }
};
