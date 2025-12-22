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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // monthly, annual, expense, budget, loan, net-worth
            $table->string('format'); // pdf, excel
            $table->string('file_path');
            $table->json('parameters')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
