<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DataSubjectRightsController extends Controller
{
    /**
     * Export a copy of the authenticated user's personal data (NDPR Art. 3.1(6) – right of access/portability).
     */
    public function export(Request $request)
    {
        $user = $request->user();

        $export = [
            'exported_at' => now()->toIso8601String(),
            'regulation'  => 'Nigeria Data Protection Regulation (NDPR) 2019',
            'profile' => [
                'name'             => $user->name,
                'email'            => $user->email,
                'phone'            => $user->phone,
                'company_name'     => $user->company_name,
                'bio'              => $user->bio,
                'website'          => $user->website,
                'country'          => $user->country,
                'instagram_handle' => $user->instagram_handle,
                'tiktok_handle'    => $user->tiktok_handle,
                'youtube_channel'  => $user->youtube_channel,
                'twitter_handle'   => $user->twitter_handle,
                'follower_count'   => $user->follower_count,
                'created_at'       => $user->created_at?->toIso8601String(),
            ],
            'financial_summary' => [
                'balance'          => $user->balance,
                'pending_balance'  => $user->pending_balance,
                'lifetime_earnings'=> $user->lifetime_earnings,
                'payout_frequency' => $user->payout_frequency,
                // Raw account details deliberately excluded from export for security;
                // user should contact privacy@clicksintel.com for payment detail copies.
            ],
            'affiliate_stats' => [
                'tier'             => $user->tier,
                'total_clicks'     => $user->total_clicks,
                'total_conversions'=> $user->total_conversions,
                'conversion_rate'  => $user->conversion_rate,
                'referral_code'    => $user->referral_code,
            ],
        ];

        Log::info('NDPR data export', ['user_id' => $user->id]);

        return response()->json($export)
            ->header('Content-Disposition', 'attachment; filename="my-data-export.json"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Submit a data erasure request (NDPR Art. 3.1(6) – right to erasure).
     * Actual erasure is processed by an admin to allow fraud-prevention retention checks.
     */
    public function requestErasure(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
            'reason'   => ['nullable', 'string', 'max:1000'],
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'The password you entered is incorrect.']);
        }

        $user = $request->user();

        // Log the erasure request for the DPO to process within 30 days (NDPR requirement).
        Log::warning('NDPR erasure request submitted', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'reason'  => $request->reason,
            'ip'      => $request->ip(),
        ]);

        return back()->with('status', 'Your data erasure request has been received. Our Data Protection Officer will process it within 30 days in accordance with the NDPR 2019.');
    }
}

