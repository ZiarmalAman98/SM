<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Allow only users assigned to the teacher panel.
     *
     * Role authorization is preferred; the legacy type field remains
     * as a temporary compatibility fallback.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Access denied. Please sign in.');
        }

        if ($user->hasRole('teacher') || $user->type === 'teacher') {
            return $next($request);
        }

        return match ($user->type) {
            'admin' => redirect('/admin'),
            'student' => redirect('/student'),
            'guardian' => redirect('/parent'),
            default => abort(403, 'Access denied. Teacher access is required.'),
        };
    }
}
