<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            // If not logged in, redirect to login
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'admin') {
            // Authenticated but NOT admin — respond with 403 forbidden
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
