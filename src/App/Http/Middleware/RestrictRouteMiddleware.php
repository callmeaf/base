<?php

namespace Callmeaf\Base\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->query('restrict_key') !== \Base::config('restrict_route_middleware_key')) {
            abort(404);
        }

        return $next($request);
    }
}
