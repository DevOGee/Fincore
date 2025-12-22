<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityService;

class LogUserActivity
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Skip logging for certain routes
        if ($this->shouldLog($request)) {
            $this->logRequest($request);
        }

        return $next($request);
    }

    protected function shouldLog(Request $request): bool
    {
        $ignorePaths = [
            'horizon',
            'telescope',
            'livewire',
            '_debugbar',
            'api/*',
        ];

        foreach ($ignorePaths as $path) {
            if ($request->is($path)) {
                return false;
            }
        }

        return true;
    }

    protected function logRequest(Request $request): void
    {
        try {
            $user = $request->user();
            
            // Log sensitive actions
            if ($this->isSensitiveAction($request)) {
                $this->logSensitiveAction($request, $user);
            }

            // Log general request (optional, can be enabled/disabled via config)
            if (config('security.log_all_requests', false)) {
                $this->logRequestDetails($request, $user);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to log request: ' . $e->getMessage());
        }
    }

    protected function isSensitiveAction(Request $request): bool
    {
        $sensitiveMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $sensitivePaths = [
            'password',
            'email',
            'profile',
            'settings',
            'admin',
        ];

        return in_array($request->method(), $sensitiveMethods) || 
               $request->is($sensitivePaths);
    }

    protected function logSensitiveAction(Request $request, $user): void
    {
        $action = $this->getActionName($request);
        
        $this->securityService->logSecurityEvent(
            $user ? $user->id : null,
            $action,
            $request->ip(),
            $request->userAgent(),
            true,
            [
                'method' => $request->method(),
                'path' => $request->path(),
                'input' => $this->sanitizeInput($request->all()),
            ]
        );
    }

    protected function logRequestDetails(Request $request, $user): void
    {
        DB::table('request_logs')->insert([
            'user_id' => $user ? $user->id : null,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function getActionName(Request $request): string
    {
        $path = $request->path();
        $method = strtolower($request->method());
        
        if (str_contains($path, 'password')) {
            return 'password_change';
        }
        
        if (str_contains($path, 'email')) {
            return 'email_change';
        }
        
        if (str_contains($path, 'admin')) {
            return 'admin_action';
        }
        
        return $method . '_' . str_replace('/', '_', $path);
    }

    protected function sanitizeInput(array $input): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'api_token',
            'credit_card',
            'cvv',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = '***REDACTED***';
            }
        }

        return $input;
    }
}
