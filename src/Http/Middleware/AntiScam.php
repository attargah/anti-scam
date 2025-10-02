<?php

namespace Attargah\AntiScam\Http\Middleware;

use Attargah\AntiScam\Exceptions\EmptyKeyException;
use Attargah\AntiScam\Models\BlockedIp;
use Attargah\AntiScam\Models\BlockedIpLog;
use Attargah\AntiScam\Models\ScamIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AntiScam
{

    /**
     * @throws EmptyKeyException
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (config('anti-scam.scam.active',false)) {
            $key = config('anti-scam.key');

            if (empty($key)) {
                throw new  EmptyKeyException();
            }
            $isRandom = config('anti-scam.scam.order_random',false);
            $inputs = config('anti-scam.scam.inputs');
               $inputs = collect($request->all())
                ->filter(fn($value, $key) => in_array($key, array_column($inputs,'name')))
                ->toArray();

            $isCheck = false;
            $isScam = false;
            $x = count($inputs) -1;
            foreach ($inputs as $name => $input) {
                if (!empty($input)) {
                    if ($isCheck) {

                        $isScam = true;
                        break;
                    }
                    $text = $key . ',' . $x . ',' . $name;
                    if (Hash::check($text, $input)) {
                        $isCheck = true;
                    } else {
                        $isScam = true;
                        break;
                    }
                }
                $x--;
            }
            if ($isScam){

                $ip = $request->ip();
                if (config('anti-scam.scam.ban',false)) {
                    BlockedIpLog::query()->create([
                        'ip_address' => $ip,
                        'form_identity' => $request->input('form_identity', __('anti-scam::anti-scam.form_identity_not_found')),
                        'reason' => __('anti-scam::anti-scam.scam_reason'),
                        'user_agent' => request()->userAgent(),
                        'request_url' => url()->full(),
                        'request_path' => request()->path(),
                        'request_method' => request()->method(),
                    ]);

                    BlockedIp::updateOrCreate(['ip_address' => $ip],['expires_at' => null]);
                }

                if (config('anti-scam.scam.save_log',false)) {
                    ScamIp::query()->create([
                        'ip_address' => $ip,
                        'form_identity' => $request->input('form_identity', __('anti-scam::anti-scam.form_identity_not_found')),
                        'reason' => __('anti-scam::anti-scam.scam_reason'),
                        'user_agent' => request()->userAgent(),
                        'request_url' => url()->full(),
                        'request_path' => request()->path(),
                        'request_method' => request()->method(),
                    ]);
                }

            }

            $request->merge([
                'scam_status_'.config('key') => $isScam
            ]);
        }

        return $next($request);
    }
}
