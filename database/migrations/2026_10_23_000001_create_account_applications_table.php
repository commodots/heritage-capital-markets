<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->string('account_type', 30)->index();
            $table->string('applicant_name', 200)->nullable();
            $table->string('email', 190)->nullable()->index();
            $table->string('phone', 50)->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->json('form_data')->nullable();
            $table->json('documents')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_applications');
    }
};
