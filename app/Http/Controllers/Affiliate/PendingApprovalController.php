<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PendingApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If already approved, redirect to the appropriate dashboard
        if ($user->is_verified) {
            if ($user->hasRole('affiliate')) {
                return redirect()->route('affiliate.dashboard');
            }

            if ($user->hasRole('advertiser')) {
                return redirect()->route('advertiser.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return Inertia::render('Affiliate/PendingApproval', [
            'emailVerified' => $user->hasVerifiedEmail(),
            'userRole' => $user->roles->first()?->name ?? 'user',
        ]);
    }
}

