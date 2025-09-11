<?php

namespace Attargah\AntiScam\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class XSSProtection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->merge($this->clean($request->all()));
        return $next($request);
    }

    /**
     * Recursive sanitize function
     */
    protected function clean(array $inputs)
    {
        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $inputs[$key] = $this->clean($value);
            } else {
                $inputs[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
            }
        }
        return $inputs;
    }
}
