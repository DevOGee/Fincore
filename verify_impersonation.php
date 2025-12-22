<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "Starting Impersonation Verification...\n";

// 1. Setup Data
$admin = User::whereHas('role', function ($q) {
    $q->where('name', 'admin'); })->first();
$targetUser = User::whereHas('role', function ($q) {
    $q->where('name', 'user'); })->first();

if (!$admin || !$targetUser) {
    echo "FAILED: Missing Admin or User for test.\n";
    exit(1);
}

// 2. Simulate Admin Login
Auth::login($admin);
echo "Logged in as Admin: {$admin->name} (ID: {$admin->id})\n";

// 3. Test Impersonation Logic (Controller Logic Simulation)
if (!auth()->user()->isAdmin()) {
    echo "FAILED: User is not recognized as admin.\n";
    exit(1);
}

// Store session
session()->put('impersonate_by', auth()->id());
Session::save(); // Ensure session is simulated/saved

// Switch user
Auth::login($targetUser);
echo "Switched to User: {$targetUser->name} (ID: {$targetUser->id})\n";

// Check session
if (!session()->has('impersonate_by')) {
    echo "FAILED: Session key 'impersonate_by' missing.\n";
} else {
    echo "PASSED: Session key 'impersonate_by' exists. Value: " . session('impersonate_by') . "\n";
}


// 4. Test Stop Impersonation Logic
$impersonatorId = session('impersonate_by');
if ($impersonatorId != $admin->id) {
    echo "FAILED: Session ID mismatch. Expected {$admin->id}, got {$impersonatorId}\n";
} else {
    echo "PASSED: Session ID matches Admin.\n";
}

// Clear session
session()->forget('impersonate_by');
Auth::loginUsingId($impersonatorId);

if (Auth::id() == $admin->id) {
    echo "PASSED: Successfully returned to Admin account.\n";
} else {
    echo "FAILED: Did not return to Admin account. Current ID: " . Auth::id() . "\n";
}

echo "Verification Complete.\n";
