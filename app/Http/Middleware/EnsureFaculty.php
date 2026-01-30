<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFaculty
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        if (! auth()->user()->isFaculty()) {
            abort(403, 'Access denied. Only Faculty can access this page.');
        }

        return $next($request);
    }
}
