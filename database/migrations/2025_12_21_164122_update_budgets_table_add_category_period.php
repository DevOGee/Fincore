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
        Schema::table('budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('budgets', 'expense_category_id')) {
                $table->foreignId('expense_category_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('budgets', 'period')) {
                $table->string('period')->default('monthly'); // monthly, yearly
            }
            if (!Schema::hasColumn('budgets', 'flexibility')) {
                $table->string('flexibility')->default('soft'); // strict, soft
            }
            if (!Schema::hasColumn('budgets', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('budgets', 'end_date')) {
                $table->date('end_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
            $table->dropColumn(['expense_category_id', 'period', 'flexibility', 'start_date', 'end_date']);
        });
    }
};
