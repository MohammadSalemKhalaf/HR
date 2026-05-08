<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        if (! $user->company && ! $user->employee && ! \App\Models\Company::where('ownerId', Auth::id())->exists()) {
            abort(403);
        }

        return $next($request);
    }
}
