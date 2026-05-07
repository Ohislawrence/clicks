<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdvertiserAccountApprovedNotification;
use App\Notifications\AdvertiserAccountRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->role, function ($query, $role) {
                $query->role($role);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(20)
            ->through(function ($user) {
                // Ensure balance fields are never null
                $user->balance = $user->balance ?? 0;
                $user->pending_balance = $user->pending_balance ?? 0;
                $user->lifetime_earnings = $user->lifetime_earnings ?? 0;
                return $user;
            });

        $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(User $user)
    {
        $user->load('roles', 'offers', 'affiliateLinks', 'conversions');

        // Get user stats
        $stats = [
            'total_offers' => $user->offers()->count(),
            'total_links' => $user->affiliateLinks()->count(),
            'total_conversions' => $user->conversions()->count(),
            'total_earnings' => $user->lifetime_earnings,
            'balance' => $user->balance,
            'pending_balance' => $user->pending_balance,
        ];

        // Get referral cap data if user is affiliate
        $referralCapData = null;
        if ($user->hasRole('affiliate')) {
            $referralCapData = [
                'cap_type' => $user->referral_cap_type,
                'cap_amount' => $user->referral_cap_amount,
                'cap_months' => $user->referral_cap_months,
                'current_earnings' => $user->referral_earnings,
                'started_at' => $user->referral_started_at,
                'cap_reached_at' => $user->referral_cap_reached_at,
                'is_active' => $user->isReferralActive(),
                'has_reached_cap' => $user->hasReachedReferralCap(),
                'progress' => $user->getReferralCapProgress(),
                'remaining_amount' => $user->getRemainingReferralCap(),
                'remaining_months' => $user->getRemainingReferralMonths(),
            ];
        }

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $stats,
            'referralCapData' => $referralCapData,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'roles' => 'required|array',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'is_active' => $validated['is_active'] ?? $user->is_active,
            'is_verified' => $validated['is_verified'] ?? $user->is_verified,
            'balance' => $validated['balance'] ?? $user->balance,
        ]);

        // Sync roles
        $user->syncRoles($validated['roles']);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }

    public function impersonate(User $user)
    {
        session(['impersonate' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', "Now viewing as {$user->name}");
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonate')) {
            $adminId = session('impersonate');
            session()->forget('impersonate');
            auth()->loginUsingId($adminId);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Stopped impersonating.');
    }

    /**
     * Approve advertiser account
     */
    public function approveAdvertiser(User $user)
    {
        if (!$user->hasRole('advertiser')) {
            return back()->with('error', 'User is not an advertiser.');
        }

        if ($user->is_verified) {
            return back()->with('info', 'Advertiser account is already approved.');
        }

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Send approval notification
        $user->notify(new AdvertiserAccountApprovedNotification());

        return back()->with('success', 'Advertiser account approved successfully!');
    }

    /**
     * Reject advertiser account
     */
    public function rejectAdvertiser(Request $request, User $user)
    {
        if (!$user->hasRole('advertiser')) {
            return back()->with('error', 'User is not an advertiser.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user->update([
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Send rejection notification
        $user->notify(new AdvertiserAccountRejectedNotification($validated['rejection_reason'] ?? null));

        return back()->with('success', 'Advertiser account rejected.');
    }

    /**
     * Update referral cap settings for an affiliate
     */
    public function updateReferralCap(Request $request, User $user)
    {
        if (!$user->hasRole('affiliate')) {
            return back()->with('error', 'User is not an affiliate.');
        }

        $validated = $request->validate([
            'referral_cap_type' => 'required|in:unlimited,amount,time,both',
            'referral_cap_amount' => 'nullable|numeric|min:0|required_if:referral_cap_type,amount,both',
            'referral_cap_months' => 'nullable|integer|min:1|required_if:referral_cap_type,time,both',
        ]);

        $updateData = [
            'referral_cap_type' => $validated['referral_cap_type'],
        ];

        // Reset cap fields based on type
        if ($validated['referral_cap_type'] === 'unlimited') {
            $updateData['referral_cap_amount'] = null;
            $updateData['referral_cap_months'] = null;
            $updateData['referral_cap_reached_at'] = null;
        } else {
            if (in_array($validated['referral_cap_type'], ['amount', 'both'])) {
                $updateData['referral_cap_amount'] = $validated['referral_cap_amount'];
            } else {
                $updateData['referral_cap_amount'] = null;
            }

            if (in_array($validated['referral_cap_type'], ['time', 'both'])) {
                $updateData['referral_cap_months'] = $validated['referral_cap_months'];
            } else {
                $updateData['referral_cap_months'] = null;
            }

            // Check if cap was reached previously but new limits allow more
            if ($user->hasReachedReferralCap()) {
                // If extending limits, reset cap_reached_at if now active
                if ($user->isReferralActive()) {
                    $updateData['referral_cap_reached_at'] = null;
                }
            }
        }

        $user->update($updateData);

        return back()->with('success', 'Referral cap settings updated successfully.');
    }
}

