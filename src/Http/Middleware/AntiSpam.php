<?php

namespace Attargah\AntiScam\Http\Middleware;

use Attargah\AntiScam\Models\BlockedIp;
use Attargah\AntiScam\Models\BlockedIpLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AntiSpam
{
    protected int $maxRequestsPerWindow = 5;
    protected int $windowInSeconds  = 60;
    protected int $permanentBanThresholdMinutes  = 10080;
    protected int $banDurationMultiplier = 3;

    public function __construct()
    {
        $this->maxRequestsPerWindow = config('anti-scam.spam.max_requests_per_window',5);
        $this->windowInSeconds = config('anti-scam.spam.window_in_seconds',60) ;
        $this->permanentBanThresholdMinutes = config('anti-scam.spam.permanent_ban_threshold_min',10080);
        $this->banDurationMultiplier = config('anti-scam.spam.ban_duration_multiplier',3);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (config('anti-scam.spam.active',false)) {
            $ip = $request->ip();
            $key = "spam_protection:$ip";

            $requests = Cache::get($key, 0);

            if ($requests >= $this->maxRequestsPerWindow) {

                $last = BlockedIpLog::getLastExpiresTime($ip);
                $isPerma = false;


                if (!empty($last) and $this->permanentBanThresholdMinutes < $last) {
                    $isPerma = true;
                }

                $expiresAt = $isPerma ? null : now()->addMinutes($last * $this->banDurationMultiplier);

                BlockedIpLog::query()->create([
                    'ip_address' => $ip,
                    'expires_at' => $expiresAt,
                    'form_identity' => $request->input('form_identity', __('anti-scam::anti-scam.form_identity_not_found')),
                    'reason' => __('anti-scam::anti-scam.max_request_exceeded', ['count' => $this->maxRequestsPerWindow]),
                    'user_agent' => request()->userAgent(),
                    'request_url' => url()->full(),
                    'request_path' => request()->path(),
                    'request_method' => request()->method(),
                ]);

              BlockedIp::updateOrCreate(['ip_address' => $ip],['expires_at' => $expiresAt]);

                Cache::forget($key);
                abort(403);
            }

            Cache::put($key, $requests + 1, $this->windowInSeconds);
        }

        return $next($request);


    }
}
