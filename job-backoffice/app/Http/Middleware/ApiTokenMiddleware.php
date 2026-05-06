<?php

namespace App\Http\Middleware;

use App\Services\ApiTokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Api-Token');

        $user = app(ApiTokenService::class)->resolve($token);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
            ], 401);
        }

        Auth::setUser($user);
        $request->attributes->set('api_user', $user);

        return $next($request);
    }
}
