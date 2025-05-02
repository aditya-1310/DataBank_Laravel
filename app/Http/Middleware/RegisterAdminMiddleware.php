<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class RegisterAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Register the admin middleware
        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->prependMiddlewareToGroup('web', \App\Http\Middleware\AdminMiddleware::class);
        
        // Store the middleware in the router
        $router = app('router');
        $router->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        
        return $next($request);
    }
}
