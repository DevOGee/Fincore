<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // 'user' or 'admin'
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role', 'permission_id']);
        });

        // Grant all permissions to admin role
        $permissions = \DB::table('permissions')->pluck('id');
        foreach ($permissions as $permissionId) {
            \DB::table('role_permissions')->insert([
                'role' => 'admin',
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
