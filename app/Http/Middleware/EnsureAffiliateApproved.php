<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliateApproved
{
    /**
     * Handle an incoming request.
     * Blocks affiliate routes until admin has approved the account (is_verified = true).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('affiliate') && !$user->is_verified) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is pending admin approval.'], 403);
            }

            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}

