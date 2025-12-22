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
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->decimal('monthly_contribution', 10, 2)->nullable();
            $table->string('funding_source')->default('surplus'); // income_percentage, surplus, legacy
            $table->date('start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->dropColumn(['monthly_contribution', 'funding_source', 'start_date']);
        });
    }
};
