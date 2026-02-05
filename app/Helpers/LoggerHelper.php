<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LoggerHelper
{
    public static function info(string $message, array $context = []): void
    {
        Log::channel('structured')->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::channel('structured')->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        Log::channel('structured')->warning($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        Log::channel('structured')->debug($message, $context);
    }

    public static function apiRequest(string $method, string $path, array $context = []): void
    {
        self::info('API Request', array_merge([
            'method' => $method,
            'path' => $path,
            'type' => 'api_request',
        ], $context));
    }

    public static function apiResponse(int $statusCode, string $path, array $context = []): void
    {
        self::info('API Response', array_merge([
            'status_code' => $statusCode,
            'path' => $path,
            'type' => 'api_response',
        ], $context));
    }
}
