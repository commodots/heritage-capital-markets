<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 100)->unique();
            $table->string('name');
            $table->decimal('value', 15, 4);
            $table->decimal('change_value', 15, 4);
            $table->decimal('change_percent', 8, 4);
            $table->timestamp('as_of');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_quotes');
    }
};
