<?php

use App\Http\Controllers\Affiliate\AffiliateLinkController;
use App\Http\Controllers\Affiliate\DashboardController;
use App\Http\Controllers\Affiliate\OfferController;
use App\Http\Controllers\Affiliate\PayoutController;
use App\Http\Controllers\Affiliate\DocumentationController as AffiliateDocumentationController;
use App\Http\Controllers\Advertiser\DashboardController as AdvertiserDashboardController;
use App\Http\Controllers\Advertiser\OfferController as AdvertiserOfferController;
use App\Http\Controllers\Advertiser\ConversionController;
use App\Http\Controllers\Advertiser\AccessRequestController;
use App\Http\Controllers\Advertiser\DocumentationController as AdvertiserDocumentationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\ConversionController as AdminConversionController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\PaymentDetailsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Tracking Routes (Public)
Route::get('/track/{trackingCode}', [TrackingController::class, 'track'])->name('track');
Route::post('/postback', [TrackingController::class, 'postback'])->name('postback');
Route::get('/pixel', [TrackingController::class, 'pixel'])->name('pixel');

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Custom Registration Routes (must be before Fortify routes)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showChoice'])->name('register');
    Route::get('/register/affiliate', [RegisterController::class, 'showAffiliateForm'])->name('register.affiliate');
    Route::post('/register/affiliate', [RegisterController::class, 'registerAffiliate'])->name('register.affiliate.store');
    Route::get('/register/advertiser', [RegisterController::class, 'showAdvertiserForm'])->name('register.advertiser');
    Route::post('/register/advertiser', [RegisterController::class, 'registerAdvertiser'])->name('register.advertiser.store');
});

// Front Pages Routes (Blade-based)
Route::get('/', [FrontPageController::class, 'home'])->name('front.home');
Route::get('/about', [FrontPageController::class, 'about'])->name('front.about');
Route::get('/features', [FrontPageController::class, 'features'])->name('front.features');
Route::get('/for-affiliates', [FrontPageController::class, 'forAffiliates'])->name('front.for-affiliates');
Route::get('/for-advertisers', [FrontPageController::class, 'forAdvertisers'])->name('front.for-advertisers');
Route::get('/store-builder{slash?}', [FrontPageController::class, 'storeBuilder'])->where('slash', '\/')->name('front.store-builder');
Route::get('/faq', [FrontPageController::class, 'faq'])->name('front.faq');
Route::get('/privacy', [FrontPageController::class, 'privacy'])->name('front.privacy');
Route::get('/terms', [FrontPageController::class, 'terms'])->name('front.terms');
Route::get('/contact', [FrontPageController::class, 'contact'])->name('front.contact');
Route::post('/contact', [FrontPageController::class, 'submitContact'])->name('front.contact.submit');

// Blog Routes (Public)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Redirect based on role
        if ($user->hasRole('affiliate')) {
            return redirect()->route('affiliate.dashboard');
        } elseif ($user->hasRole('advertiser')) {
            return redirect()->route('advertiser.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Payment Details Route
    Route::put('/user/payment-details', [PaymentDetailsController::class, 'update'])->name('user.payment-details.update');
});

// Affiliate Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:affiliate',
])->prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Offers
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
    Route::post('/offers/{offer}/request-access', [OfferController::class, 'requestAccess'])->name('offers.request-access');

    // Affiliate Links
    Route::get('/links', [AffiliateLinkController::class, 'index'])->name('links.index');
    Route::post('/links', [AffiliateLinkController::class, 'store'])->name('links.store');
    Route::patch('/links/{affiliateLink}/toggle', [AffiliateLinkController::class, 'toggle'])->name('links.toggle');
    Route::delete('/links/{affiliateLink}', [AffiliateLinkController::class, 'destroy'])->name('links.destroy');
    Route::post('/links/{affiliateLink}/whatsapp', [AffiliateLinkController::class, 'generateWhatsApp'])->name('links.whatsapp');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Affiliate\ReportController::class, 'index'])->name('reports.index');

    // Documentation
    Route::get('/documentation', [AffiliateDocumentationController::class, 'index'])->name('documentation.index');

    // Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/create', [PayoutController::class, 'create'])->name('payouts.create');
    Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::delete('/payouts/{payout}/cancel', [PayoutController::class, 'cancel'])->name('payouts.cancel');
});

// Advertiser Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:advertiser',
])->prefix('advertiser')->name('advertiser.')->group(function () {
    Route::get('/dashboard', [AdvertiserDashboardController::class, 'index'])->name('dashboard');

    // Offers Management
    Route::get('/offers', [AdvertiserOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/create', [AdvertiserOfferController::class, 'create'])->name('offers.create');
    Route::post('/offers', [AdvertiserOfferController::class, 'store'])->name('offers.store');
    Route::get('/offers/{offer}', [AdvertiserOfferController::class, 'show'])->name('offers.show');
    Route::get('/offers/{offer}/edit', [AdvertiserOfferController::class, 'edit'])->name('offers.edit');
    Route::put('/offers/{offer}', [AdvertiserOfferController::class, 'update'])->name('offers.update');
    Route::delete('/offers/{offer}', [AdvertiserOfferController::class, 'destroy'])->name('offers.destroy');
    Route::patch('/offers/{offer}/toggle', [AdvertiserOfferController::class, 'toggle'])->name('offers.toggle');

    // Creatives Management
    Route::get('/offers/{offer}/creatives', [\App\Http\Controllers\Advertiser\CreativeController::class, 'index'])->name('creatives.index');
    Route::post('/offers/{offer}/creatives', [\App\Http\Controllers\Advertiser\CreativeController::class, 'store'])->name('creatives.store');
    Route::post('/offers/{offer}/creatives/{creative}', [\App\Http\Controllers\Advertiser\CreativeController::class, 'update'])->name('creatives.update');
    Route::delete('/offers/{offer}/creatives/{creative}', [\App\Http\Controllers\Advertiser\CreativeController::class, 'destroy'])->name('creatives.destroy');
    Route::patch('/offers/{offer}/creatives/{creative}/toggle', [\App\Http\Controllers\Advertiser\CreativeController::class, 'toggleStatus'])->name('creatives.toggle');

    // Conversions Management
    Route::get('/conversions', [ConversionController::class, 'index'])->name('conversions.index');
    Route::get('/conversions/create', [ConversionController::class, 'create'])->name('conversions.create');
    Route::post('/conversions', [ConversionController::class, 'store'])->name('conversions.store');
    Route::post('/conversions/{conversion}/approve', [ConversionController::class, 'approve'])->name('conversions.approve');
    Route::post('/conversions/{conversion}/reject', [ConversionController::class, 'reject'])->name('conversions.reject');
    Route::post('/conversions/bulk-approve', [ConversionController::class, 'bulkApprove'])->name('conversions.bulk-approve');

    // Manual Conversions (WhatsApp Tracking)
    Route::get('/manual-conversions', [\App\Http\Controllers\Advertiser\ManualConversionController::class, 'index'])->name('manual-conversions.index');
    Route::post('/manual-conversions', [\App\Http\Controllers\Advertiser\ManualConversionController::class, 'store'])->name('manual-conversions.store');
    Route::post('/manual-conversions/bulk-import', [\App\Http\Controllers\Advertiser\ManualConversionController::class, 'bulkImport'])->name('manual-conversions.bulk-import');

    // Access Requests Management
    Route::get('/access-requests', [AccessRequestController::class, 'index'])->name('access-requests.index');
    Route::post('/access-requests/{accessRequest}/approve', [AccessRequestController::class, 'approve'])->name('access-requests.approve');
    Route::post('/access-requests/{accessRequest}/reject', [AccessRequestController::class, 'reject'])->name('access-requests.reject');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Advertiser\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Advertiser\ReportController::class, 'export'])->name('reports.export');

    // Documentation
    Route::get('/documentation', [AdvertiserDocumentationController::class, 'index'])->name('documentation.index');

    // Store Management
    Route::get('/stores', [\App\Http\Controllers\Advertiser\StoreController::class, 'index'])->name('store.index');
    Route::get('/store/setup', [\App\Http\Controllers\Advertiser\StoreController::class, 'setup'])->name('store.setup');
    Route::post('/store/setup', [\App\Http\Controllers\Advertiser\StoreController::class, 'createStore'])->name('store.create');
    Route::get('/store/{store}/dashboard', [\App\Http\Controllers\Advertiser\StoreController::class, 'dashboard'])->name('store.dashboard');
    Route::get('/store/{store}/edit', [\App\Http\Controllers\Advertiser\StoreController::class, 'edit'])->name('store.edit');
    Route::put('/store/{store}', [\App\Http\Controllers\Advertiser\StoreController::class, 'update'])->name('store.update');
    Route::get('/store/{store}/preview', [\App\Http\Controllers\Advertiser\StoreController::class, 'preview'])->name('store.preview');
    Route::post('/store/{store}/publish', [\App\Http\Controllers\Advertiser\StoreController::class, 'publish'])->name('store.publish');
    Route::post('/store/{store}/unpublish', [\App\Http\Controllers\Advertiser\StoreController::class, 'unpublish'])->name('store.unpublish');

    // Store Products Management
    Route::get('/store/{store}/products', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'index'])->name('store.products.index');
    Route::get('/store/{store}/products/create', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'create'])->name('store.products.create');
    Route::post('/store/{store}/products', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'store'])->name('store.products.store');
    Route::get('/store/{store}/products/{product}/edit', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'edit'])->name('store.products.edit');
    Route::put('/store/{store}/products/{product}', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'update'])->name('store.products.update');
    Route::delete('/store/{store}/products/{product}', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'destroy'])->name('store.products.destroy');
    Route::patch('/store/{store}/products/{product}/toggle', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'toggle'])->name('store.products.toggle');
    Route::patch('/store/{store}/products/{product}/featured', [\App\Http\Controllers\Advertiser\StoreProductController::class, 'toggleFeatured'])->name('store.products.featured');

    // Store Categories Management
    Route::get('/store/{store}/categories', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'index'])->name('store.categories.index');
    Route::get('/store/{store}/categories/create', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'create'])->name('store.categories.create');
    Route::post('/store/{store}/categories', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'store'])->name('store.categories.store');
    Route::get('/store/{store}/categories/{category}/edit', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'edit'])->name('store.categories.edit');
    Route::put('/store/{store}/categories/{category}', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'update'])->name('store.categories.update');
    Route::delete('/store/{store}/categories/{category}', [\App\Http\Controllers\Advertiser\StoreCategoryController::class, 'destroy'])->name('store.categories.destroy');

    // Store Discount Codes Management
    Route::get('/store/{store}/discount-codes', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'index'])->name('store.discount-codes.index');
    Route::get('/store/{store}/discount-codes/create', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'create'])->name('store.discount-codes.create');
    Route::post('/store/{store}/discount-codes', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'store'])->name('store.discount-codes.store');
    Route::get('/store/{store}/discount-codes/{discountCode}/edit', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'edit'])->name('store.discount-codes.edit');
    Route::put('/store/{store}/discount-codes/{discountCode}', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'update'])->name('store.discount-codes.update');
    Route::delete('/store/{store}/discount-codes/{discountCode}', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'destroy'])->name('store.discount-codes.destroy');
    Route::patch('/store/{store}/discount-codes/{discountCode}/toggle', [\App\Http\Controllers\Advertiser\StoreDiscountCodeController::class, 'toggle'])->name('store.discount-codes.toggle');

    // Store Subscription Management
    Route::get('/store/{store}/subscription', [\App\Http\Controllers\Advertiser\StoreSubscriptionController::class, 'index'])->name('store.subscription.index');
    Route::post('/store/{store}/subscription/renew', [\App\Http\Controllers\Advertiser\StoreSubscriptionController::class, 'initiateRenewal'])->name('store.subscription.renew');
    Route::get('/store/{store}/subscription/verify', [\App\Http\Controllers\Advertiser\StoreSubscriptionController::class, 'verifyPayment'])->name('store.subscription.verify');
    Route::post('/store/{store}/subscription/change-plan', [\App\Http\Controllers\Advertiser\StoreSubscriptionController::class, 'changePlan'])->name('store.subscription.change-plan');

    // Store Orders Management
    Route::get('/store/{store}/orders', [\App\Http\Controllers\Advertiser\StoreOrderController::class, 'index'])->name('store.orders.index');
    Route::get('/store/{store}/orders/{order}', [\App\Http\Controllers\Advertiser\StoreOrderController::class, 'show'])->name('store.orders.show');
    Route::patch('/store/{store}/orders/{order}/status', [\App\Http\Controllers\Advertiser\StoreOrderController::class, 'updateStatus'])->name('store.orders.update-status');
    Route::patch('/store/{store}/orders/{order}/mark-paid', [\App\Http\Controllers\Advertiser\StoreOrderController::class, 'markAsPaid'])->name('store.orders.mark-paid');

    // Store Analytics
    Route::get('/store/{store}/analytics', [\App\Http\Controllers\Advertiser\StoreAnalyticsController::class, 'index'])->name('store.analytics');

    // Store Theme Customizer
    Route::get('/store/{store}/theme', [\App\Http\Controllers\Advertiser\StoreController::class, 'themeCustomizer'])->name('store.theme');
    Route::post('/store/{store}/theme', [\App\Http\Controllers\Advertiser\StoreController::class, 'updateTheme'])->name('store.theme.update');
});

// Admin Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/stop-impersonating', [AdminUserController::class, 'stopImpersonating'])->name('stop-impersonating');
    Route::post('/users/{user}/approve-advertiser', [AdminUserController::class, 'approveAdvertiser'])->name('users.approve-advertiser');
    Route::post('/users/{user}/reject-advertiser', [AdminUserController::class, 'rejectAdvertiser'])->name('users.reject-advertiser');
    Route::put('/users/{user}/referral-cap', [AdminUserController::class, 'updateReferralCap'])->name('users.update-referral-cap');

    // Offer Management
    Route::get('/offers', [AdminOfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/{offer}', [AdminOfferController::class, 'show'])->name('offers.show');
    Route::post('/offers/{offer}/toggle', [AdminOfferController::class, 'toggle'])->name('offers.toggle');
    Route::delete('/offers/{offer}', [AdminOfferController::class, 'destroy'])->name('offers.destroy');
    Route::post('/offers/{offer}/featured', [AdminOfferController::class, 'featured'])->name('offers.featured');
    Route::post('/offers/{offer}/approve', [AdminOfferController::class, 'approve'])->name('offers.approve');
    Route::post('/offers/{offer}/reject', [AdminOfferController::class, 'reject'])->name('offers.reject');

    // Conversion Management
    Route::get('/conversions', [AdminConversionController::class, 'index'])->name('conversions.index');

    // Payout Management
    Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{payout}/process', [AdminPayoutController::class, 'process'])->name('payouts.process');
    Route::post('/payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');
    Route::post('/payouts/{payout}/mark-completed', [AdminPayoutController::class, 'markCompleted'])->name('payouts.mark-completed');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/export/stats', [\App\Http\Controllers\Admin\ReportController::class, 'exportStats'])->name('reports.export.stats');
    Route::get('/reports/export/daily-stats', [\App\Http\Controllers\Admin\ReportController::class, 'exportDailyStats'])->name('reports.export.daily-stats');
    Route::get('/reports/export/clicks', [\App\Http\Controllers\Admin\ReportController::class, 'exportClicks'])->name('reports.export.clicks');
    Route::get('/reports/export/conversions', [\App\Http\Controllers\Admin\ReportController::class, 'exportConversions'])->name('reports.export.conversions');

    // Blacklist Management (Phase 4)
    Route::get('/blacklists', [\App\Http\Controllers\Admin\BlacklistController::class, 'index'])->name('blacklists.index');
    Route::post('/blacklists', [\App\Http\Controllers\Admin\BlacklistController::class, 'store'])->name('blacklists.store');
    Route::put('/blacklists/{blacklist}', [\App\Http\Controllers\Admin\BlacklistController::class, 'update'])->name('blacklists.update');
    Route::delete('/blacklists/{blacklist}', [\App\Http\Controllers\Admin\BlacklistController::class, 'destroy'])->name('blacklists.destroy');
    Route::post('/blacklists/bulk-destroy', [\App\Http\Controllers\Admin\BlacklistController::class, 'bulkDestroy'])->name('blacklists.bulk-destroy');
    Route::patch('/blacklists/{blacklist}/toggle', [\App\Http\Controllers\Admin\BlacklistController::class, 'toggleActive'])->name('blacklists.toggle');

    // Blog Management
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');

    // Blog Categories
    Route::get('/blog/categories', [AdminBlogController::class, 'categories'])->name('blog.categories');
    Route::post('/blog/categories', [AdminBlogController::class, 'storeCategory'])->name('blog.categories.store');
    Route::put('/blog/categories/{category}', [AdminBlogController::class, 'updateCategory'])->name('blog.categories.update');
    Route::delete('/blog/categories/{category}', [AdminBlogController::class, 'destroyCategory'])->name('blog.categories.destroy');
    Route::post('/blacklists/import', [\App\Http\Controllers\Admin\BlacklistController::class, 'import'])->name('blacklists.import');
    Route::get('/blacklists/export', [\App\Http\Controllers\Admin\BlacklistController::class, 'export'])->name('blacklists.export');
    Route::post('/blacklists/test', [\App\Http\Controllers\Admin\BlacklistController::class, 'test'])->name('blacklists.test');
    Route::get('/blacklists/stats', [\App\Http\Controllers\Admin\BlacklistController::class, 'stats'])->name('blacklists.stats');

    // Store Plans Management
    Route::get('/store-plans', [\App\Http\Controllers\Admin\StorePlanController::class, 'index'])->name('store-plans.index');
    Route::get('/store-plans/create', [\App\Http\Controllers\Admin\StorePlanController::class, 'create'])->name('store-plans.create');
    Route::post('/store-plans', [\App\Http\Controllers\Admin\StorePlanController::class, 'store'])->name('store-plans.store');
    Route::get('/store-plans/{storePlan}/edit', [\App\Http\Controllers\Admin\StorePlanController::class, 'edit'])->name('store-plans.edit');
    Route::put('/store-plans/{storePlan}', [\App\Http\Controllers\Admin\StorePlanController::class, 'update'])->name('store-plans.update');
    Route::delete('/store-plans/{storePlan}', [\App\Http\Controllers\Admin\StorePlanController::class, 'destroy'])->name('store-plans.destroy');
    Route::patch('/store-plans/{storePlan}/toggle', [\App\Http\Controllers\Admin\StorePlanController::class, 'toggle'])->name('store-plans.toggle');

    // Store Themes Management
    Route::get('/store-themes', [\App\Http\Controllers\Admin\StoreThemeController::class, 'index'])->name('store-themes.index');
    Route::get('/store-themes/create', [\App\Http\Controllers\Admin\StoreThemeController::class, 'create'])->name('store-themes.create');
    Route::post('/store-themes', [\App\Http\Controllers\Admin\StoreThemeController::class, 'store'])->name('store-themes.store');
    Route::get('/store-themes/{storeTheme}/edit', [\App\Http\Controllers\Admin\StoreThemeController::class, 'edit'])->name('store-themes.edit');
    Route::put('/store-themes/{storeTheme}', [\App\Http\Controllers\Admin\StoreThemeController::class, 'update'])->name('store-themes.update');
    Route::delete('/store-themes/{storeTheme}', [\App\Http\Controllers\Admin\StoreThemeController::class, 'destroy'])->name('store-themes.destroy');
    Route::patch('/store-themes/{storeTheme}/toggle', [\App\Http\Controllers\Admin\StoreThemeController::class, 'toggle'])->name('store-themes.toggle');

    // Store Management (Manage User Stores)
    Route::get('/stores', [\App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{store}', [\App\Http\Controllers\Admin\StoreController::class, 'show'])->name('stores.show');
    Route::get('/stores/{store}/edit', [\App\Http\Controllers\Admin\StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [\App\Http\Controllers\Admin\StoreController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{store}', [\App\Http\Controllers\Admin\StoreController::class, 'destroy'])->name('stores.destroy');
    Route::post('/stores/{store}/toggle-status', [\App\Http\Controllers\Admin\StoreController::class, 'toggleStatus'])->name('stores.toggle-status');
    Route::post('/stores/{store}/toggle-published', [\App\Http\Controllers\Admin\StoreController::class, 'togglePublished'])->name('stores.toggle-published');
    Route::post('/stores/{store}/extend-subscription', [\App\Http\Controllers\Admin\StoreController::class, 'extendSubscription'])->name('stores.extend-subscription');
    Route::post('/stores/{store}/cancel-subscription', [\App\Http\Controllers\Admin\StoreController::class, 'cancelSubscription'])->name('stores.cancel-subscription');

    // Store Analytics (Platform-wide)
    Route::get('/store-analytics', [\App\Http\Controllers\Admin\StoreAnalyticsController::class, 'index'])->name('store-analytics');
});

// ============================================================================
// PUBLIC STOREFRONT ROUTES
// ============================================================================
// These routes handle the public-facing storefronts created by advertisers
// Format: /store/{business-slug}

Route::prefix('store')->name('storefront.')->group(function () {
    // Store Homepage (single or multi-product based on store type)
    Route::get('/{slug}', [\App\Http\Controllers\StorefrontController::class, 'show'])->name('show');
    Route::get('/{slug}/shop', [\App\Http\Controllers\StorefrontController::class, 'shop'])->name('shop');
    Route::get('/{slug}/about', [\App\Http\Controllers\StorefrontController::class, 'about'])->name('about');

    // Product Detail Page (multi-product stores only)
    Route::get('/{slug}/product/{productSlug}', [\App\Http\Controllers\StorefrontController::class, 'product'])->name('product');

    // Checkout Process
    Route::post('/{slug}/checkout', [\App\Http\Controllers\StorefrontController::class, 'checkout'])->name('checkout');
    Route::get('/{slug}/checkout/verify', [\App\Http\Controllers\StorefrontController::class, 'verifyPayment'])->name('checkout.verify');

    // Order Confirmation
    Route::get('/{slug}/order/{orderNumber}/thank-you', [\App\Http\Controllers\StorefrontController::class, 'thankYou'])->name('thank-you');
});
