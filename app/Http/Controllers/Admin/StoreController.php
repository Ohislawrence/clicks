<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\StorePlan;
use App\Models\StoreSubscription;
use App\Models\StoreTheme;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of all stores
     */
    public function index(Request $request)
    {
        $query = Store::with(['user', 'plan', 'theme'])
            ->withCount(['products', 'orders']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by subscription status
        if ($request->filled('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('store_plan_id', $request->plan);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $stores = $query->paginate(20)->withQueryString();

        // Get plans for filter
        $plans = StorePlan::where('is_active', true)->get();

        // Get stats
        $stats = [
            'total' => Store::count(),
            'active' => Store::where('is_active', true)->count(),
            'inactive' => Store::where('is_active', false)->count(),
            'expired' => Store::where('subscription_status', 'expired')->count(),
            'revenue_this_month' => StoreSubscription::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return Inertia::render('Admin/Stores/Index', [
            'stores' => $stores,
            'plans' => $plans,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'subscription_status' => $request->subscription_status,
                'plan' => $request->plan,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Display the specified store
     */
    public function show(Store $store)
    {
        $store->load([
            'user',
            'plan',
            'theme',
            'products' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'orders' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            },
            'subscriptions' => function ($query) {
                $query->with('plan')->orderBy('created_at', 'desc');
            },
        ]);

        // Calculate stats
        $stats = [
            'total_products' => $store->products()->count(),
            'active_products' => $store->products()->where('is_active', true)->count(),
            'total_orders' => $store->orders()->count(),
            'pending_orders' => $store->orders()->where('fulfillment_status', 'pending')->count(),
            'total_revenue' => $store->orders()->where('payment_status', 'paid')->sum('total'),
            'revenue_this_month' => $store->orders()
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
        ];

        return Inertia::render('Admin/Stores/Show', [
            'store' => $store,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified store
     */
    public function edit(Store $store)
    {
        $store->load(['user', 'plan', 'theme']);

        $plans = StorePlan::where('is_active', true)->get();
        $themes = StoreTheme::where('is_active', true)->get();

        return Inertia::render('Admin/Stores/Edit', [
            'store' => $store,
            'plans' => $plans,
            'themes' => $themes,
        ]);
    }

    /**
     * Update the specified store
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug,' . $store->id,
            'description' => 'nullable|string',
            'store_plan_id' => 'required|exists:store_plans,id',
            'store_theme_id' => 'required|exists:store_themes,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'subscription_status' => 'required|in:active,expired,cancelled',
            'subscription_payment_gateway' => 'required|in:paystack,flutterwave',
            'is_active' => 'boolean',
        ]);

        $store->update($validated);

        return redirect()->route('admin.stores.show', $store)
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Toggle store active status
     */
    public function toggleStatus(Store $store)
    {
        $store->update([
            'is_active' => !$store->is_active,
        ]);

        $status = $store->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Store {$status} successfully.");
    }

    /**
     * Toggle store published status
     */
    public function togglePublished(Store $store)
    {
        $store->update([
            'published_at' => $store->published_at ? null : now(),
        ]);

        $status = $store->published_at ? 'published' : 'unpublished';

        return back()->with('success', "Store {$status} successfully.");
    }

    /**
     * Extend subscription
     */
    public function extendSubscription(Request $request, Store $store)
    {
        $validated = $request->validate([
            'duration' => 'required|in:1_month,3_months,6_months,1_year',
            'reason' => 'nullable|string',
        ]);

        $durations = [
            '1_month' => 1,
            '3_months' => 3,
            '6_months' => 6,
            '1_year' => 12,
        ];

        $months = $durations[$validated['duration']];

        // Extend from current end date or now if expired
        $currentEnd = $store->subscription_end_date && $store->subscription_end_date->isFuture()
            ? $store->subscription_end_date
            : now();

        $store->update([
            'subscription_end_date' => $currentEnd->addMonths($months),
            'subscription_status' => 'active',
        ]);

        return back()->with('success', "Subscription extended by {$months} month(s).");
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request, Store $store)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $store->update([
            'subscription_status' => 'cancelled',
            'is_active' => false,
        ]);

        return back()->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Delete the specified store
     */
    public function destroy(Store $store)
    {
        DB::beginTransaction();
        try {
            // Delete related data
            $store->products()->delete();
            $store->orders()->delete();

            // Delete the store
            $store->delete();

            DB::commit();

            return redirect()->route('admin.stores.index')
                ->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }
}
