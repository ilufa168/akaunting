<?php

namespace App\Http\Middleware;

use Closure;

class CanInstall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if app is installed via env var
        $envInstalled = env('APP_INSTALLED', false);
        
        // Also check if database has migrations table (actual installation state)
        $dbInstalled = false;
        try {
            $dbInstalled = \DB::connection()->getPdo() && \DB::schema()->hasTable('migrations');
        } catch (\Exception $e) {
            $dbInstalled = false;
        }
        
        // App needs installation if BOTH env says not installed AND no migrations table
        if ($envInstalled == false && !$dbInstalled) {
            return $next($request);
        }

        // Already installed, redirect to login
        return redirect()->route('login');
    }
}
