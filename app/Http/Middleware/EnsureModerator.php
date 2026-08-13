<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModerator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isModerator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => 'Forbidden',
                ], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
