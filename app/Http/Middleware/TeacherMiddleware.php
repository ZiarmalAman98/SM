<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->user() ||
            ($request->user()->type != 'teacher')
        ) {
            if ($request->user()->type == 'admin') {
                return redirect('/admin');
            }
            if ($request->user()->type == 'student') {
                return redirect('/student');
            }
            if ($request->user()->type == 'guardian') {
                return redirect('/parent');
            }
            abort(403, 'Access denied. Only teachers can access this page.');
        }

        return $next($request);
    }
}
