<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Access denied. Please sign in as parent.');
        }

        if ($user->type !== 'guardian') {
            if ($user->type == 'staff') {
                return $next($request);
            }
            if ($user->type == 'student') {
                return redirect('/student');
            }
            if ($user->type == 'teacher') {
                return redirect('/teacher');
            }
            if ($user->type == 'admin') {
                return redirect('/admin');
            }
            abort(403, 'Access denied. Only parent can access this page.');
        }
        return $next($request);
    }
}
