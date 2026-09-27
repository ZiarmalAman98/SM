<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    /**
     * Allow only users assigned to the parent panel.
     *
     * Role authorization is preferred; the legacy guardian type remains
     * as a temporary compatibility fallback.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Access denied. Please sign in as a parent.');
        }

        if ($user->hasRole('parent') || $user->type === 'guardian') {
            return $next($request);
        }

        return match ($user->type) {
            'admin' => redirect('/admin'),
            'teacher' => redirect('/teacher'),
            'student' => redirect('/student'),
            default => abort(403, 'Access denied. Parent access is required.'),
        };
    }
}
