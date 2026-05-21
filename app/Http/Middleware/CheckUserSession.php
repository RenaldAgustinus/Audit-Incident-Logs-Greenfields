<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // Kalau tidak ada sesi user_id, tendang balik ke halaman login
    if (!$request->session()->has('user_id')) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }
    return $next($request);
}
}
