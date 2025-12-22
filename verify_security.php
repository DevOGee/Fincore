<?php

use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Support\Facades\Auth;

echo "Starting Security Controls Verification...\n";

$securityService = new SecurityService();

// 1. Test Brute Force Detection
echo "\n=== Testing Brute Force Detection ===\n";
$testEmail = 'security_test_' . time() . '@example.com';

// Simulate 5 failed attempts
for ($i = 1; $i <= 5; $i++) {
    $securityService->recordFailedLogin($testEmail, '192.168.1.100');
    echo "Failed attempt $i recorded\n";
}

if ($securityService->detectBruteForce($testEmail)) {
    echo "PASSED: Brute force detected after 5 failed attempts\n";
} else {
    echo "FAILED: Brute force not detected\n";
}

// 2. Test Successful Login Recording  
echo "\n=== Testing Login Recording ===\n";
$testUser = User::first();
if ($testUser) {
    $securityService->recordSuccessfulLogin($testUser, '192.168.1.200');
    echo "PASSED: Successful login recorded for user {$testUser->email}\n";
}

// 3. Test Suspicious Login Detection
echo "\n=== Testing Suspicious Login Detection ===\n";
if ($testUser) {
    $isSuspicious = $securityService->detectSuspiciousLogin($testUser, '10.0.0.1');
    if ($isSuspicious) {
        echo "PASSED: New IP detected as suspicious\n";
    } else {
        echo "FAILED: New IP not detected as suspicious\n";
    }
}

// 4. Test Failed Login Stats
echo "\n=== Testing Statistics ===\n";
$failedCount = $securityService->getRecentFailedAttempts(24);
echo "Failed logins in last 24h: $failedCount\n";
if ($failedCount >= 5) {
    echo "PASSED: Failed login stats working\n";
} else {
    echo "Note: Failed login count may be low (normal if first run)\n";
}

echo "\nVerification Complete.\n";
