<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set CORS headers
        $response->headers->set('Access-Control-Allow-Origin', 'https://kejacrm.netlify.app');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Handle preflight requests (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            $response->headers->set('Access-Control-Max-Age', '86400');
            return $response;
        }

        return $response;
    }
}
