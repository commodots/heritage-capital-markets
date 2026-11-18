<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->unsignedInteger('subscription_version')->default(1);
        });

        DB::table('newsletter_subscribers')
            ->where('is_active', true)
            ->whereNull('subscribed_at')
            ->update(['subscribed_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropColumn(['subscribed_at', 'unsubscribed_at', 'subscription_version']);
        });
    }
};
