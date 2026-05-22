<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PendingApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If already approved, redirect to dashboard
        if ($user->is_verified) {
            return redirect()->route('affiliate.dashboard');
        }

        return Inertia::render('Affiliate/PendingApproval', [
            'emailVerified' => $user->hasVerifiedEmail(),
        ]);
    }
}
