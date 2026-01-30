<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectFacultyToPortal
{
    /**
     * Redirect faculty users from home (/) to faculty dashboard.
     * Only runs when path is exactly / to avoid redirecting from other routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('/') && auth()->check() && auth()->user()->isFaculty()) {
            return redirect()->route('faculty.dashboard');
        }

        return $next($request);
    }
}
