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
        // 1. Create default roles if not exist (handled by seeder later or here)
        // Ensure roles table exists first (it's in a previous migration file in the list)

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
        });

        // 2. Migrate existing 'role' string data to 'role_id'
        // We need to seed roles first to get IDs
        $adminRole = \DB::table('roles')->where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRoleId = \DB::table('roles')->insertGetId([
                'name' => 'admin',
                'description' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        $userRole = \DB::table('roles')->where('name', 'user')->first();
        if (!$userRole) {
            $userRoleId = \DB::table('roles')->insertGetId([
                'name' => 'user',
                'description' => 'Regular User',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $userRoleId = $userRole->id;
        }

        // Update users based on string role
        \DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRoleId]);
        \DB::table('users')->where('role', 'user')->update(['role_id' => $userRoleId]);
        // Default any nulls to user
        \DB::table('users')->whereNull('role_id')->update(['role_id' => $userRoleId]);

        // 3. Drop old role column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // 4. Handle Permission Migration
        // Existing table 'role_permissions' uses string 'role'. We should drop it and use 'permission_role' which uses IDs.
        Schema::dropIfExists('role_permissions');

        // Seed permission_role
        // Give admin all permissions
        $permissions = \DB::table('permissions')->pluck('id');
        foreach ($permissions as $permId) {
            \DB::table('permission_role')->insertOrIgnore([
                'role_id' => $adminRoleId,
                'permission_id' => $permId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
        });

        // Restore role strings
        $roles = \DB::table('roles')->pluck('name', 'id');
        foreach ($roles as $id => $name) {
            \DB::table('users')->where('role_id', $id)->update(['role' => $name]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
