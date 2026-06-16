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
        if (
            !$request->user() ||
            ($request->user()->type != 'guardian')
        ) {
            if ($request->user()->type == 'staff') {
                return $next($request);
            }
            if ($request->user()->type == 'student') {
                return redirect('/student');
            }
            if ($request->user()->type == 'teacher') {
                return redirect('/teacher');
            }
            if ($request->user()->type == 'admin') {
                return redirect('/admin');
            }
            abort(403, 'Access denied. Only parent can access this page.');
        }
        return $next($request);
    }
}
