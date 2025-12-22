<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(SecurityService $securityService)
    {
        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', User::STATUS_ACTIVE)->count();
        $suspendedUsers = User::where('status', User::STATUS_SUSPENDED)->count();
        $deletedUsers = User::onlyTrashed()->count();

        // Security Statistics
        $failedLogins24h = $securityService->getRecentFailedAttempts(24);
        $recentAttempts = $securityService->getRecentAttempts(20);

        // Audit Logs (recent)
        $auditLogs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // System Health (basic)
        $systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'deletedUsers',
            'failedLogins24h',
            'recentAttempts',
            'auditLogs',
            'systemHealth'
        ));
    }

    private function checkDatabaseHealth(): string
    {
        try {
            DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'unhealthy';
        }
    }

    private function checkStorageHealth(): string
    {
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usagePercent > 90) {
            return 'critical';
        } elseif ($usagePercent > 75) {
            return 'warning';
        }
        return 'healthy';
    }
}
