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

        // Define allowed origins, methods, and headers
        $allowedOrigins = ['http://localhost:5173', 'https://kejacrm.netlify.app']; // Specify allowed origins
        $origin = $request->headers->get('Origin');

        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', ''); // Or set to an empty string for security
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Handle preflight requests (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            $response->headers->set('Access-Control-Max-Age', '86400'); // Cache preflight response for 1 day
            return $response;
        }

        return $response;
    }
}
