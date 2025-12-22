<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecurityService
{
    /**
     * Check if email has too many failed login attempts
     */
    public function detectBruteForce(string $email): bool
    {
        $recentAttempts = DB::table('login_attempts')
            ->where('email', $email)
            ->where('success', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes(15))
            ->count();

        return $recentAttempts >= 5;
    }

    /**
     * Record failed login attempt
     */
    public function recordFailedLogin(string $email, string $ip): void
    {
        DB::table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ip,
            'success' => false,
            'attempted_at' => Carbon::now(),
        ]);
    }

    /**
     * Record successful login
     */
    public function recordSuccessfulLogin(User $user, string $ip): void
    {
        DB::table('login_attempts')->insert([
            'email' => $user->email,
            'ip_address' => $ip,
            'success' => true,
            'attempted_at' => Carbon::now(),
        ]);

        // Clear old failed attempts for this user
        DB::table('login_attempts')
            ->where('email', $user->email)
            ->where('success', false)
            ->where('attempted_at', '<', Carbon::now()->subHours(1))
            ->delete();
    }

    /**
     * Detect suspicious login (new IP address)
     */
    public function detectSuspiciousLogin(User $user, string $ip): bool
    {
        $knownIp = DB::table('login_attempts')
            ->where('email', $user->email)
            ->where('ip_address', $ip)
            ->where('success', true)
            ->exists();

        return !$knownIp;
    }

    /**
     * Get failed login attempts in last 24 hours
     */
    public function getRecentFailedAttempts(int $hours = 24): int
    {
        return DB::table('login_attempts')
            ->where('success', false)
            ->where('attempted_at', '>=', Carbon::now()->subHours($hours))
            ->count();
    }

    /**
     * Get recent login attempts for display
     */
    public function getRecentAttempts(int $limit = 50)
    {
        return DB::table('login_attempts')
            ->orderBy('attempted_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
