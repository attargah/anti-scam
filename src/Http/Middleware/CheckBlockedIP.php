<?php

namespace Attargah\AntiScam\Http\Middleware;

use Attargah\AntiScam\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $ip = $request->getClientIp();

        $blocked = BlockedIp::isBlocked($ip);

        if ($blocked) {
            abort(403);
        }

        return $next($request);
    }
}
