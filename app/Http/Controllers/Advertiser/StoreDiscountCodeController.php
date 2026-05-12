<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\StoreDiscountCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreDiscountCodeController extends Controller
{
    public function index($storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        $codes = $store->discountCodes()->latest()->get();

        return Inertia::render('Advertiser/Store/DiscountCodes/Index', [
            'store' => $store->load('plan'),
            'codes' => $codes,
        ]);
    }

    public function create($storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        return Inertia::render('Advertiser/Store/DiscountCodes/Form', [
            'store' => $store,
            'discountCode' => null,
        ]);
    }

    public function store(Request $request, $storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        $validated = $request->validate([
            'code' => 'required|string|max:50|alpha_dash',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Percentage cap
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%.']);
        }

        // Ensure code is uppercase and unique within store
        $validated['code'] = strtoupper($validated['code']);

        if ($store->discountCodes()->where('code', $validated['code'])->exists()) {
            return back()->withErrors(['code' => 'This discount code already exists in your store.']);
        }

        $store->discountCodes()->create($validated);

        return redirect()->route('advertiser.store.discount-codes.index', $store->id)
            ->with('success', 'Discount code created successfully.');
    }

    public function edit($storeId, StoreDiscountCode $discountCode)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($discountCode->store_id !== $store->id) {
            abort(403);
        }

        return Inertia::render('Advertiser/Store/DiscountCodes/Form', [
            'store' => $store,
            'discountCode' => $discountCode,
        ]);
    }

    public function update(Request $request, $storeId, StoreDiscountCode $discountCode)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($discountCode->store_id !== $store->id) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|alpha_dash',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%.']);
        }

        $validated['code'] = strtoupper($validated['code']);

        if ($store->discountCodes()->where('code', $validated['code'])->where('id', '!=', $discountCode->id)->exists()) {
            return back()->withErrors(['code' => 'This discount code already exists in your store.']);
        }

        $discountCode->update($validated);

        return redirect()->route('advertiser.store.discount-codes.index', $store->id)
            ->with('success', 'Discount code updated successfully.');
    }

    public function destroy($storeId, StoreDiscountCode $discountCode)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($discountCode->store_id !== $store->id) {
            abort(403);
        }

        $discountCode->delete();

        return redirect()->route('advertiser.store.discount-codes.index', $store->id)
            ->with('success', 'Discount code deleted.');
    }

    public function toggle($storeId, StoreDiscountCode $discountCode)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($discountCode->store_id !== $store->id) {
            abort(403);
        }

        $discountCode->update(['is_active' => !$discountCode->is_active]);

        return back()->with('success', 'Discount code status updated.');
    }
}
