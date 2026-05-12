<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StorePlanController extends Controller
{
    /**
     * Display a listing of store plans.
     */
    public function index()
    {
        $plans = StorePlan::withCount('stores')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/StorePlans/Index', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return Inertia::render('Admin/StorePlans/Create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:store_plans',
            'store_type' => 'required|in:single,multi',
            'product_limit' => 'nullable|integer|min:1',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'yearly_discount_percent' => 'nullable|integer|min:0|max:100',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Calculate discount percentage if not provided
        if (!isset($validated['yearly_discount_percent'])) {
            $monthlyTotal = $validated['monthly_price'] * 12;
            if ($monthlyTotal > 0 && $validated['yearly_price'] < $monthlyTotal) {
                $validated['yearly_discount_percent'] = round((($monthlyTotal - $validated['yearly_price']) / $monthlyTotal) * 100);
            }
        }

        StorePlan::create($validated);

        return redirect()->route('admin.store-plans.index')
            ->with('success', 'Store plan created successfully.');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(StorePlan $storePlan)
    {
        return Inertia::render('Admin/StorePlans/Edit', [
            'plan' => $storePlan,
        ]);
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, StorePlan $storePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:store_plans,slug,' . $storePlan->id,
            'store_type' => 'required|in:single,multi',
            'product_limit' => 'nullable|integer|min:1',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'yearly_discount_percent' => 'nullable|integer|min:0|max:100',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Calculate discount percentage if not provided
        if (!isset($validated['yearly_discount_percent'])) {
            $monthlyTotal = $validated['monthly_price'] * 12;
            if ($monthlyTotal > 0 && $validated['yearly_price'] < $monthlyTotal) {
                $validated['yearly_discount_percent'] = round((($monthlyTotal - $validated['yearly_price']) / $monthlyTotal) * 100);
            }
        }

        $storePlan->update($validated);

        return redirect()->route('admin.store-plans.index')
            ->with('success', 'Store plan updated successfully.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(StorePlan $storePlan)
    {
        // Check if plan is in use
        if ($storePlan->stores()->count() > 0) {
            return back()->with('error', 'Cannot delete plan that is currently in use by stores.');
        }

        $storePlan->delete();

        return redirect()->route('admin.store-plans.index')
            ->with('success', 'Store plan deleted successfully.');
    }

    /**
     * Toggle the active status of a plan.
     */
    public function toggle(StorePlan $storePlan)
    {
        $storePlan->update([
            'is_active' => !$storePlan->is_active,
        ]);

        $status = $storePlan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Store plan {$status} successfully.");
    }
}
