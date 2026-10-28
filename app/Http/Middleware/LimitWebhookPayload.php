<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitWebhookPayload
{
    private const MAX_BYTES = 262144;

    public function handle(Request $request, Closure $next): Response
    {
        if ((int) $request->server('CONTENT_LENGTH', 0) > self::MAX_BYTES
            || strlen($request->getContent()) > self::MAX_BYTES) {
            return response()->json(['message' => 'Payload Too Large'], 413);
        }

        return $next($request);
    }
}
