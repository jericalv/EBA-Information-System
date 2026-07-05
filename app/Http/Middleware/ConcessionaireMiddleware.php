<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConcessionaireMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'concessionaire') {
            abort(403, 'Unauthorized. Concessionaire access only.');
        }

        if (! $request->user()->is_approved) {
            return redirect()->route('home')
                ->with('error', 'Concessionaire tools are available after approval. Use Settings -> My Application to complete your documents.');
        }

        return $next($request);
    }
}
