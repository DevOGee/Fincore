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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // budget, expense, loan
            $table->string('severity'); // info, warning, critical
            $table->string('title');
            $table->text('message');
            $table->string('alertable_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('alertable_id')->nullable(); // Polymorphic relation
            $table->json('metadata')->nullable(); // Additional data like amounts, percentages
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
