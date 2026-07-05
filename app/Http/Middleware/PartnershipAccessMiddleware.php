<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnershipAccessMiddleware
{
    /**
     * Allow partnership endpoints for authenticated non-admin users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role === 'admin') {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
