<?php

namespace Callmeaf\Base\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next,int $status,...$except): Response
    {
        if(! empty($except)) {
            foreach ($except as $url) {
                if(str($url)->contains('/')) {
                    if($request->is($url)) {
                        return $next($request);
                    }
                } else if (str($url)->contains('.')) {
                    if($request->routeIs($url)) {
                        return $next($request);
                    }
                }
            }
        }

        abort($status);
    }
}
