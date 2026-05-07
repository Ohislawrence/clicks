<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TrafficSource;
use App\Notifications\WelcomeAffiliateNotification;
use App\Notifications\WelcomeAdvertiserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

class RegisterController extends Controller
{
    /**
     * Show the account type selection page
     */
    public function showChoice()
    {
        return Inertia::render('Auth/RegisterChoice');
    }

    /**
     * Show the affiliate registration form
     */
    public function showAffiliateForm()
    {
        return Inertia::render('Auth/RegisterAffiliate');
    }

    /**
     * Show the advertiser registration form
     */
    public function showAdvertiserForm()
    {
        return Inertia::render('Auth/RegisterAdvertiser');
    }

    /**
     * Handle affiliate registration
     */
    public function registerAffiliate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'country' => ['required', 'string', 'max:255'],
            'traffic_sources' => ['required', 'array', 'min:1'],
            'traffic_sources.*.type' => ['required', 'in:instagram,tiktok,youtube,twitter,facebook,website,blog,other'],
            'traffic_sources.*.name' => ['required', 'string', 'max:255'],
            'traffic_sources.*.url' => ['required', 'url', 'max:500'],
            'traffic_sources.*.followers' => ['nullable', 'integer', 'min:0'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'bio' => $request->bio,
                'country' => $request->country,
                'is_verified' => false,
                'is_active' => true,
            ]);

            // Assign affiliate role
            $user->assignRole('affiliate');

            // Create traffic sources
            foreach ($request->traffic_sources as $index => $source) {
                TrafficSource::create([
                    'user_id' => $user->id,
                    'type' => $source['type'],
                    'name' => $source['name'],
                    'url' => $source['url'],
                    'followers' => $source['followers'] ?? null,
                    'is_primary' => $index === 0, // First one is primary
                ]);
            }

            DB::commit();

            event(new Registered($user));

            // Send welcome notification
            $user->notify(new WelcomeAffiliateNotification());

            Auth::login($user);

            return redirect()->route('affiliate.dashboard')->with('success', 'Welcome! Your affiliate account has been created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle advertiser registration
     */
    public function registerAdvertiser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:500'],
            'country' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'website' => $request->website,
                'country' => $request->country,
                'bio' => $request->bio,
                'is_verified' => false,
                'is_active' => true,
            ]);

            // Assign advertiser role
            $user->assignRole('advertiser');

            DB::commit();

            event(new Registered($user));

            // Send welcome notification
            $user->notify(new WelcomeAdvertiserNotification());

            // Notify all admins about new advertiser account
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewAdvertiserAccountNotification($user));
            }

            Auth::login($user);

            return redirect()->route('advertiser.dashboard')->with('success', 'Welcome! Your advertiser account has been created. It will be reviewed by our team.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}
