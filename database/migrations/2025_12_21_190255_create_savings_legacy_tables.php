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
        Schema::create('savings_allocation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // emergency, wealth, legacy
            $table->decimal('percentage', 5, 2); // e.g., 10.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('savings_legacy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('income_id')->nullable()->constrained()->onDelete('set null');
            $table->string('category'); // emergency, wealth, legacy
            $table->decimal('amount', 15, 2);
            $table->decimal('percentage_applied', 5, 2);
            $table->date('date');
            $table->string('month'); // YYYY-MM
            $table->string('quarter'); // Q1, Q2, etc.
            $table->integer('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_legacy');
        Schema::dropIfExists('savings_allocation_rules');
    }
};
