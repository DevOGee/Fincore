<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "Starting Verification...\n";

// 1. Verify Roles exist
$adminRole = Role::where('name', 'admin')->first();
$userRole = Role::where('name', 'user')->first();

if (!$adminRole || !$userRole) {
    echo "FAILED: Roles missing.\n";
    exit(1);
}
echo "PASSED: Roles exist (Admin ID: {$adminRole->id}, User ID: {$userRole->id})\n";

// 2. Create User via Model (simulating Registration)
$testUserEmail = 'verify_' . time() . '@example.com';
$user = User::create([
    'name' => 'Verify User',
    'email' => $testUserEmail,
    'password' => Hash::make('password'),
    'status' => 'active',
]);

// Handle automatic role assignment (if booted method works)
$user->refresh();
if (!$user->role || $user->role->name !== 'user') {
    echo "FAILED: Default role not assigned. Current Role: " . ($user->role ? $user->role->name : 'None') . "\n";
    // Try manual assignment
    $user->assignRole($userRole);
    $user->refresh();
} else {
    echo "PASSED: Default role assigned automatically.\n";
}

// 3. Test isAdmin
if ($user->isAdmin()) {
    echo "FAILED: New user shouldn't be admin.\n";
} else {
    echo "PASSED: New user is not admin.\n";
}

// 4. Assign Admin Role
$user->assignRole('admin');
$user->refresh();

if ($user->isAdmin()) {
    echo "PASSED: User promoted to Admin.\n";
} else {
    echo "FAILED: User not promoted to Admin. Role: " . ($user->role ? $user->role->name : 'None') . "\n";
}

// 5. Test specific permissions (assuming admin has all)
if ($user->hasPermission('view_users')) {
    echo "PASSED: Admin has 'view_users' permission.\n";
} else {
    echo "FAILED: Admin missing 'view_users' permission.\n";
}

// Clean up
$user->delete();
echo "Verification Complete.\n";
