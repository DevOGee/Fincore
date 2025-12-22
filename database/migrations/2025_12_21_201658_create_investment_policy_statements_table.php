<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investment_policy_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('risk_profile')->default('moderate'); // conservative, moderate, aggressive
            $table->json('allocation_targets')->nullable(); // {"stocks": 50, "bonds": 20, ...}
            $table->decimal('max_single_asset_pct', 5, 2)->default(25.00);
            $table->string('rebalance_frequency')->default('quarterly'); // monthly, quarterly, annually
            $table->timestamps();

            $table->unique('user_id'); // One IPS per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_policy_statements');
    }
};
