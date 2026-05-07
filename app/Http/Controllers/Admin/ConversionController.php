<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversionController extends Controller
{
    public function index(Request $request)
    {
        $conversions = Conversion::with(['offer.advertiser', 'affiliate', 'affiliateLink'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->offer_id, function ($query, $offerId) {
                $query->where('offer_id', $offerId);
            })
            ->when($request->affiliate_id, function ($query, $affiliateId) {
                $query->where('affiliate_id', $affiliateId);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', "%{$search}%")
                      ->orWhereHas('affiliate', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(20);

        $offers = Offer::select('id', 'name')->get();
        $affiliates = User::role('affiliate')->select('id', 'name')->get();

        return Inertia::render('Admin/Conversions/Index', [
            'conversions' => $conversions,
            'offers' => $offers,
            'affiliates' => $affiliates,
            'filters' => [
                'status' => $request->status,
                'offer_id' => $request->offer_id,
                'affiliate_id' => $request->affiliate_id,
                'search' => $request->search,
            ],
        ]);
    }
}
