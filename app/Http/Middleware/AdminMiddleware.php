<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in at all - redirect to admin login
        if (! $request->user()) {
            return redirect()->route('admin.login');
        }

        // Logged in but not an admin
        if (! $request->user()->isAdmin()) {
            abort(403, 'Access denied. You do not have admin privileges.');
        }

        return $next($request);
    }
}
