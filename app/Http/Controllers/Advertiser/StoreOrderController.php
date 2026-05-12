<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\StoreOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreOrderController extends Controller
{
    /**
     * Display a listing of store orders
     */
    public function index(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $query = $store->orders()->latest();

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by fulfillment status
        if ($request->filled('fulfillment_status')) {
            $query->where('fulfillment_status', $request->fulfillment_status);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = [
            'total' => $store->orders()->count(),
            'pending_payment' => $store->orders()->where('payment_status', 'pending')->count(),
            'paid' => $store->orders()->where('payment_status', 'paid')->count(),
            'pending_fulfillment' => $store->orders()->where('fulfillment_status', 'pending')->count(),
            'total_revenue' => $store->orders()->where('payment_status', 'paid')->sum('total'),
        ];

        return Inertia::render('Advertiser/Store/Orders/Index', [
            'orders' => $orders,
            'store' => $store,
            'stats' => $stats,
            'filters' => $request->only(['payment_status', 'fulfillment_status', 'search']),
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($storeId, StoreOrder $order)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure order belongs to user's store
        if ($order->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Advertiser/Store/Orders/Show', [
            'order' => $order,
            'store' => $store,
        ]);
    }

    /**
     * Update order fulfillment status
     */
    public function updateStatus(Request $request, $storeId, StoreOrder $order)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure order belongs to user's store
        if ($order->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'fulfillment_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        // TODO: Send notification to customer about status update

        return back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Mark an order as paid (for payment-link orders confirmed manually)
     */
    public function markAsPaid($storeId, StoreOrder $order)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if ($order->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        if ($order->payment_status === 'paid') {
            return back()->with('info', 'Order is already marked as paid.');
        }

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Order #' . $order->order_number . ' marked as paid.');
    }
}
