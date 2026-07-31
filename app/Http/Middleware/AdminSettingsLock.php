<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSettingsLock
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lockKey = 'admin_settings_unlocked_at';
        $unlockedAt = $request->session()->get($lockKey);

        if (!$unlockedAt || (time() - $unlockedAt > 900)) {
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.settings.unlock');
        }

        $request->session()->put($lockKey, time());

        return $next($request);
    }
}
