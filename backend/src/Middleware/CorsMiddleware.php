<?php

namespace App\Middleware;

/**
 * CORS middleware to handle cross-origin requests
 */
class CorsMiddleware
{
    /**
     * Handle CORS headers
     */
    public static function handle(): void
    {
        $origin = getenv('CORS_ORIGIN') ?: '*';

        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Max-Age: 3600");

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
