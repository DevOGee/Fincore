<?php

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

echo "Starting Profile Verification...\n";

// 1. Setup User
$user = User::where('email', 'like', 'verify_%')->first();
if (!$user) {
    echo "Creating new test user...\n";
    $user = User::create([
        'name' => 'Profile Test User',
        'email' => 'verify_profile_' . time() . '@example.com',
        'password' => bcrypt('password'),
        'status' => 'active',
    ]);
}
Auth::login($user);

// 2. Check Defaults
if (!$user->preference) {
    // Should be created by boot logic or on demand
    $user->preference()->create();
    $user->refresh();
}

$pref = $user->preference;
echo "Default Currency: {$pref->currency} (Expected: USD)\n";
echo "Default Risk: {$pref->risk_profile} (Expected: medium)\n";

if ($pref->currency !== 'USD' || $pref->risk_profile !== 'medium') {
    echo "FAILED: Default preferences incorrect.\n";
} else {
    echo "PASSED: Default preferences correct.\n";
}

// 3. Update Preferences
echo "Updating preferences...\n";
$user->preference->update([
    'currency' => 'EUR',
    'risk_profile' => 'high',
    'language' => 'fr',
]);
$user->refresh();

echo "Updated Currency: {$user->preference->currency} (Expected: EUR)\n";
echo "Updated Risk: {$user->preference->risk_profile} (Expected: high)\n";

if ($user->preference->currency === 'EUR' && $user->preference->risk_profile === 'high') {
    echo "PASSED: Preferences updated successfully.\n";
} else {
    echo "FAILED: Preferences update failed.\n";
}

echo "Verification Complete.\n";
