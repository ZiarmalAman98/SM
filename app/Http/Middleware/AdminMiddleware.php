<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allow only users assigned to the administrative panel.
     *
     * Authorization is role-first, with the legacy type field retained
     * temporarily for backwards compatibility during the migration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Access denied. Please sign in.');
        }

        if ($user->hasAnyRole(['super_admin', 'admin']) || $user->type === 'admin') {
            return $next($request);
        }

        return match ($user->type) {
            'student' => redirect('/student'),
            'teacher' => redirect('/teacher'),
            'guardian' => redirect('/parent'),
            default => abort(403, 'Access denied. Administrative access is required.'),
        };
    }
}
