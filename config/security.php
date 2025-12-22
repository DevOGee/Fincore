<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Logging
    |--------------------------------------------------------------------------
    |
    | This section controls the security logging features of the application.
    |
    */
    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'log_all_requests' => env('SECURITY_LOG_ALL_REQUESTS', false),
        'retention_days' => env('SECURITY_LOG_RETENTION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Management
    |--------------------------------------------------------------------------
    |
    | These settings control the user session management.
    |
    */
    'session' => [
        'invalidate_on_password_change' => true,
        'max_attempts' => 5,
        'lockout_minutes' => 15,
        'password_expiry_days' => 90,
        'password_history' => 5, // Number of previous passwords to remember
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | These settings control the two-factor authentication features.
    |
    */
    '2fa' => [
        'enabled' => env('2FA_ENABLED', true),
        'required_for_admin' => true,
        'methods' => ['email', 'authenticator'],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Security
    |--------------------------------------------------------------------------
    |
    | These settings control IP-based security features.
    |
    */
    'ip_security' => [
        'allowed_countries' => [], // Empty array allows all countries
        'block_suspicious_ips' => true,
        'max_login_attempts' => 5,
        'lockout_minutes' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | These settings control the rate limiting for various actions.
    |
    */
    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 5,
        'decay_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | These settings control the Content Security Policy headers.
    |
    */
    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
        'report_only' => env('CSP_REPORT_ONLY', false),
        'report_uri' => env('CSP_REPORT_URI', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | These settings control the security headers sent with each response.
    |
    */
    'headers' => [
        'xss_protection' => '1; mode=block',
        'content_type_options' => 'nosniff',
        'strict_transport_security' => 'max-age=31536000; includeSubDomains',
        'x_frame_options' => 'SAMEORIGIN',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => [
            'geolocation' => '()',
            'camera' => '()',
            'microphone' => '()',
            'payment' => '()',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Data
    |--------------------------------------------------------------------------
    |
    | These settings control how sensitive data is handled.
    |
    */
    'sensitive_data' => [
        'masked_fields' => [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'api_token',
            'credit_card',
            'cvv',
        ],
        'encrypt' => [
            'api_tokens',
            'access_tokens',
        ],
    ],
];
