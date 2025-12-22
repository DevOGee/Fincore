<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'start_date')) {
                $table->date('start_date')->nullable()->after('type');
            }
            if (!Schema::hasColumn('investments', 'initial_investment')) {
                $table->decimal('initial_investment', 15, 2)->default(0)->after('start_date');
            }
            if (!Schema::hasColumn('investments', 'status')) {
                $table->string('status')->default('active')->after('current_value');
            }
            if (!Schema::hasColumn('investments', 'details')) {
                $table->json('details')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'initial_investment', 'status', 'details']);
        });
    }
};
