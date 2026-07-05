<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnforceValidRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $validRoles = ['admin', 'cashier', 'concessionaire', 'student', 'faculty'];

        if (! in_array($user->role, $validRoles, true)) {
            Log::warning('Blocked user with invalid role.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'path' => $request->path(),
            ]);

            abort(403, 'Unauthorized role.');
        }

        return $next($request);
    }
}
