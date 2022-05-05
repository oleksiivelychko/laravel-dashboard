<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class AllowOnlyAjaxRequests
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->ajax()) {
            return response('Allow only AJAX requests', 405);
        } else {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
