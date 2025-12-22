<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'view_users', 'create_users'
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default permissions
        \DB::table('permissions')->insert([
            ['name' => 'view_users', 'description' => 'View all users', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'create_users', 'description' => 'Create new users', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_users', 'description' => 'Edit user details', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete_users', 'description' => 'Delete or suspend users', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
