<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_indices', function (Blueprint $table) {
    $table->id();

    $table->string('name');
    $table->string('symbol')->nullable();

    $table->decimal('value', 15, 4)->nullable();
    $table->decimal('change_value', 15, 4)->nullable();
    $table->decimal('change_percent', 8, 4)->nullable();

    $table->timestamp('as_of')->nullable();

    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_indices');
    }
};
