<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency')->default('USD');
            $table->string('timezone')->default('UTC');
            $table->string('financial_year_start')->default('01-01');
            $table->string('language')->default('en');
            $table->decimal('savings_percentage', 5, 2)->default(0);
            $table->string('risk_profile')->default('medium'); // low, medium, high
            $table->string('budget_strategy')->default('50/30/20'); // 50/30/20, zero_based, pay_yourself_first
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
