<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'platform_name' => config('app.name'),
            'minimum_payout' => Cache::get('minimum_payout', 5000),
            'commission_cap' => Cache::get('commission_cap', 50),
            'cookie_duration_max' => Cache::get('cookie_duration_max', 365),
            'cookie_duration_min' => Cache::get('cookie_duration_min', 1),
            'auto_approve_conversions' => Cache::get('auto_approve_conversions', false),
            'require_offer_approval' => Cache::get('require_offer_approval', false),
            'max_active_links_per_affiliate' => Cache::get('max_active_links_per_affiliate', 100),
            'fraud_detection_enabled' => Cache::get('fraud_detection_enabled', true),
            'max_clicks_per_ip' => Cache::get('max_clicks_per_ip', 5),
            'platform_fee_percentage' => Cache::get('platform_fee_percentage', 0),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'minimum_payout' => 'required|numeric|min:1000',
            'commission_cap' => 'required|numeric|min:0|max:100',
            'cookie_duration_max' => 'required|integer|min:1|max:365',
            'cookie_duration_min' => 'required|integer|min:1|max:365',
            'auto_approve_conversions' => 'boolean',
            'require_offer_approval' => 'boolean',
            'max_active_links_per_affiliate' => 'required|integer|min:1',
            'fraud_detection_enabled' => 'boolean',
            'max_clicks_per_ip' => 'required|integer|min:1',
            'platform_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            Cache::forever($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
