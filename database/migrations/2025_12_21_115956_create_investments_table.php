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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // stock, bond, real_estate, business, etc.
            $table->string('status')->default('active'); // active, exited
            $table->date('start_date')->nullable();
            $table->decimal('initial_investment', 15, 2)->default(0);
            $table->decimal('current_value', 15, 2)->default(0); // Cached latest valuation
            $table->json('details')->nullable(); // For custom fields like ticker symbol, address, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
