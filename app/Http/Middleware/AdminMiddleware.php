<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug information
        \Log::info('Admin Middleware Check:', [
            'is_authenticated' => Auth::check(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'is_admin' => Auth::check() ? Auth::user()->is_admin : false,
            'request_path' => $request->path()
        ]);

        if (!Auth::check()) {
            \Log::info('Not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        if (!Auth::user()->is_admin) {
            \Log::info('User is not admin, redirecting to dashboard');
            return redirect()->route('dashboard')->with('error', 'Access denied. You are not an administrator.');
        }
        
        \Log::info('User is admin, proceeding to route');
        return $next($request);
    }
}
