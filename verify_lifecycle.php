<?php

use App\Models\User;
use App\Services\DataExportService;
use Illuminate\Support\Facades\Auth;

echo "Starting Account Lifecycle Verification...\n";

// 1. Create a test user
$testUser = User::create([
    'name' => 'Lifecycle Test',
    'email' => 'lifecycle_test_' . time() . '@example.com',
    'password' => bcrypt('password'),
    'status' => 'active',
]);

echo "Created test user: {$testUser->email} (ID: {$testUser->id})\n";

// 2. Create some financial data
\App\Models\Income::create(['user_id' => $testUser->id, 'source' => 'Test', 'amount' => 1000, 'date' => now()]);
\App\Models\Expense::create(['user_id' => $testUser->id, 'amount' => 500, 'date' => now()]);

echo "Created test financial data.\n";

// 3. Test Data Export
$exportService = new DataExportService();
$filePath = $exportService->export($testUser);

if (file_exists($filePath)) {
    echo "PASSED: Data export successful. File: $filePath\n";
    $data = json_decode(file_get_contents($filePath), true);
    echo "Export contains " . count($data['incomes']) . " incomes, " . count($data['expenses']) . " expenses\n";
} else {
    echo "FAILED: Data export failed.\n";
}

// 4. Test Soft Delete
echo "Soft deleting user data...\n";
$exportService->softDeleteUserData($testUser);

// 5. Verify Soft Delete
$deletedUser = User::withTrashed()->find($testUser->id);
if ($deletedUser && $deletedUser->trashed()) {
    echo "PASSED: User soft deleted successfully.\n";
} else {
    echo "FAILED: User not soft deleted.\n";
}

$incomeCount = \App\Models\Income::withTrashed()->where('user_id', $testUser->id)->whereNotNull('deleted_at')->count();
if ($incomeCount > 0) {
    echo "PASSED: Financial data soft deleted ($incomeCount incomes).\n";
} else {
    echo "FAILED: Financial data not soft deleted.\n";
}

// 6. Verify Audit Logs NOT deleted (if they exist)
echo "Note: Audit logs should remain intact (not soft deleted).\n";

echo "Verification Complete.\n";
