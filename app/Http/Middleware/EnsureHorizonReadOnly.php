<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureHorizonReadOnly
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('queue-operations.horizon_enabled', false)) {
            abort_unless($request->isMethodSafe(), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
