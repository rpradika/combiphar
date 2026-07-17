<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps non-production hosts out of search indexes — Google indexed the old
 * webdev.combiphar.com build. Crawling stays allowed (robots.txt does not
 * Disallow) so crawlers can actually see the noindex directive.
 */
class NoIndexNonProduction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! app()->environment('production')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
