<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPrivilege
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role === 'ADMIN') {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'Admin privileges are required to perform this action.'
            ], 401);
        }
    }
}
