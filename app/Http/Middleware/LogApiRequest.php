<?php

namespace App\Http\Middleware;

use App\Helpers\LoggerHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        LoggerHelper::apiRequest(
            $request->method(),
            $request->path(),
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        LoggerHelper::apiResponse(
            $response->getStatusCode(),
            $request->path(),
            [
                'duration_ms' => $duration,
                'method' => $request->method(),
            ]
        );

        return $response;
    }
}
