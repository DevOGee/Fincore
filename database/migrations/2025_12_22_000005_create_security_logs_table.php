<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('success')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('ip_address');
        });

        // Create request logs table if not exists
        if (!Schema::hasTable('request_logs')) {
            Schema::create('request_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('method', 10);
                $table->string('path');
                $table->string('ip', 45);
                $table->text('user_agent')->nullable();
                $table->json('payload')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['method', 'path']);
                $table->index('created_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('request_logs');
    }
};
