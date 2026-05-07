# Phase 2 Implementation: Core Features

## 🚀 What Was Delivered

### 1. **Affiliate Tier System** ✅

#### **Database Schema**
Added to `users` table:
- `tier` - Enum: bronze, silver, gold, platinum (default: bronze)
- `tier_commission_bonus` - Percentage bonus (0-100%)
- `total_clicks` - Aggregated click count for tier calculation
- `total_conversions` - Aggregated conversion count
- `conversion_rate` - Calculated percentage
- `tier_updated_at` - Last tier change timestamp
- `parent_affiliate_id` - For sub-affiliate referral system
- `referral_code` - Unique referral code
- `referral_count` - Number of referred affiliates
- `referral_earnings` - Total earnings from referrals

#### **Tier Requirements**
| Tier | Min Conversions | Min Earnings | Bonus % | Color |
|------|----------------|--------------|---------|-------|
| **Bronze** | 0 | ₦0 | 0% | Gray |
| **Silver** | 50 | ₦5,000 | 5% | Slate |
| **Gold** | 200 | ₦25,000 | 10% | Yellow |
| **Platinum** | 500 | ₦100,000 | 15% | Purple |

#### **Features**
- ✅ Automatic tier upgrades based on performance
- ✅ Commission bonuses applied to all conversions
- ✅ Tier progress tracking (conversions + earnings)
- ✅ Sub-affiliate referral system (10% default commission)
- ✅ Unique referral codes generation
- ✅ Parent-child affiliate relationships
- ✅ Referral earnings tracking

#### **Service: `TierService`**
**Methods:**
- `updateAffiliateTier(User $affiliate)` - Calculate and update tier
- `calculateTier(User $affiliate)` - Determine appropriate tier
- `getTierInfo(User $affiliate)` - Get tier details and progress
- `generateReferralCode(User $affiliate)` - Generate unique code
- `processReferral()` - Handle sub-affiliate commissions
- `getLeaderboard(int $limit)` - Top affiliates by earnings

---

### 2. **Offer Caps & Budget Limits** ✅

#### **Database Schema**
Added to `offers` table:
- `daily_conversion_cap` - Max conversions per day
- `monthly_conversion_cap` - Max conversions per month
- `total_conversion_cap` - Lifetime conversion limit
- `budget_limit` - Maximum spend budget
- `spent_budget` - Current spent amount
- `today_conversions` - Daily counter
- `month_conversions` - Monthly counter
- `last_cap_reset_date` - Last reset timestamp
- `auto_pause_on_cap` - Auto-pause when cap reached (default: true)
- `pause_reason` - Why offer was paused

#### **Features**
- ✅ Daily, monthly, and lifetime conversion caps
- ✅ Budget limits with automatic tracking
- ✅ Auto-pause offers when caps reached
- ✅ Automatic daily/monthly counter resets
- ✅ Cap status tracking and reporting
- ✅ Conversion rejection when capped
- ✅ Detailed cap status percentage calculations

#### **Service: `OfferCapService`**
**Methods:**
- `hasReachedCap(Offer $offer)` - Check if any cap reached
- `checkAndPause(Offer $offer)` - Auto-pause if capped
- `incrementConversion(Offer $offer, float $commission)` - Update counters
- `resetDailyCaps()` - Reset all daily caps (scheduled task)
- `resetMonthlyCaps()` - Reset all monthly caps (scheduled task)
- `getCapStatus(Offer $offer)` - Get detailed cap information

**Cap Status Response:**
```php
[
    'daily' => ['current' => 45, 'cap' => 50, 'remaining' => 5, 'percentage' => 90],
    'monthly' => ['current' => 180, 'cap' => 200, 'remaining' => 20, 'percentage' => 90],
    'total' => ['current' => 950, 'cap' => 1000, 'remaining' => 50, 'percentage' => 95],
    'budget' => ['spent' => 45000, 'limit' => 50000, 'remaining' => 5000, 'percentage' => 90],
    'has_caps' => true,
    'is_capped' => false,
]
```

---

### 3. **Reporting Dashboard with EPC, CR, ROI** ✅

#### **Advanced Metrics Implemented**
- **EPC (Earnings Per Click)** = Total Commissions / Total Clicks
- **CR (Conversion Rate)** = (Conversions / Clicks) × 100
- **ROI (Return on Investment)** = ((Revenue - Cost) / Cost) × 100
- **EPL (Earnings Per Lead)** = Total Commissions / Total Conversions
- **Average Order Value** = Total Revenue / Total Conversions
- **Fraud Rate** = (Invalid Clicks / Total Clicks) × 100

#### **Service: `ReportingService`**
**Methods:**

**`getStats(array $filters)`** - Comprehensive statistics
Returns:
```php
[
    'period' => ['from' => '2026-04-01', 'to' => '2026-05-01', 'days' => 31],
    'clicks' => ['total' => 5000, 'valid' => 4800, 'invalid' => 200, 'fraud_rate' => 4.0],
    'conversions' => ['total' => 150, 'approved' => 140, 'pending' => 10, 'rate' => 3.0],
    'revenue' => ['total' => 150000, 'average_order_value' => 1000],
    'commissions' => ['total' => 45000, 'approved' => 42000, 'pending' => 3000],
    'performance' => ['epc' => 9.0, 'epl' => 300, 'roi' => 233.33, 'cr' => 3.0],
]
```

**`getDailyStats(array $filters)`** - Daily breakdown for charts
Returns array of daily data:
```php
[
    ['date' => '2026-04-01', 'clicks' => 150, 'conversions' => 5, 'revenue' => 5000, 'commissions' => 1500, 'cr' => 3.33, 'epc' => 10],
    ['date' => '2026-04-02', ...],
]
```

**`getTopOffers(int $limit, array $filters)`** - Best performing offers
**`getTopAffiliates(int $limit, array $filters)`** - Leaderboard
**`exportStats(array $filters)`** - Export data (CSV-ready)

#### **Filters Supported**
- `date_from` - Start date
- `date_to` - End date
- `user_id` - Filter by specific user
- `offer_id` - Filter by specific offer
- `role` - Role context (admin/affiliate/advertiser)

#### **Controllers Created**
- `Admin\ReportController` - Platform-wide reports
- `Affiliate\ReportController` - Affiliate performance + tier info
- `Advertiser\ReportController` - Advertiser campaign reports

#### **Routes Added**
- `GET /admin/reports` - Admin comprehensive dashboard
- `GET /affiliate/reports` - Affiliate performance reports
- `GET /advertiser/reports` - Advertiser campaign analytics

---

### 4. **Enhanced Conversion Tracking** ✅

#### **Updated `ProcessConversionJob`**
New Features:
- ✅ **Offer cap checking** - Reject conversions if capped
- ✅ **Tier bonus application** - Add tier bonus to commissions
- ✅ **Affiliate stats updates** - Update total_clicks, total_conversions, conversion_rate
- ✅ **Automatic tier evaluation** - Check for tier upgrades after conversion
- ✅ **Sub-affiliate processing** - Process referral commissions
- ✅ **Cap counter increments** - Update daily/monthly/total counters
- ✅ **Auto-pause logic** - Pause offers when caps reached

#### **Commission Calculation Flow**
```
1. Base Commission (from offer commission_rate)
   ↓
2. + Tier Bonus (e.g., 10% for Gold tier)
   ↓
3. - Platform Fee (configurable, e.g., 5%)
   ↓
4. Apply Commission Cap (max per conversion)
   ↓
5. Final Commission
```

**Example:**
- Base: ₦1,000
- Tier Bonus (10%): +₦100 = ₦1,100
- Platform Fee (5%): -₦55 = ₦1,045
- **Final: ₦1,045**

---

### 5. **Pixel Tracking Enhancement** ✅

**Already implemented in Phase 1:**
- ✅ `GET /pixel` route - 1x1 transparent GIF
- ✅ Cookie-based attribution
- ✅ Query parameter support (`value`, `txn_id`)

**Usage:**
```html
<!-- Place on thank-you page -->
<img src="https://dealsintel.com/pixel?value=100.00&txn_id=ORDER123" width="1" height="1" />
```

**JavaScript Snippet** (for advertisers):
```javascript
<script>
  // Send conversion via pixel
  const conversionValue = 100.00;
  const txnId = 'ORDER123';
  const img = new Image();
  img.src = `https://dealsintel.com/pixel?value=${conversionValue}&txn_id=${txnId}`;
</script>
```

---

## 📊 Database Changes

### **Migrations Run:**
1. ✅ `add_affiliate_tier_system_to_users_table` - 10 new columns
2. ✅ `add_caps_and_budgets_to_offers_table` - 10 new columns

### **Model Updates:**
1. ✅ `User` model - Added tier fields + relationships (parentAffiliate, subAffiliates)
2. ✅ `Offer` model - Added cap/budget fields + casts

---

## 🎯 How to Use

### **1. Test Tier System**

```php
// In tinker or controller
$affiliate = User::role('affiliate')->first();

// Generate referral code
$tierService = app(\App\Services\TierService::class);
$code = $tierService->generateReferralCode($affiliate);

// Get tier info
$info = $tierService->getTierInfo($affiliate);

// Manually update tier
$tierService->updateAffiliateTier($affiliate);

// View tier requirements
$requirements = $tierService->getTierRequirements();
```

### **2. Set Offer Caps**

```php
$offer = Offer::first();

$offer->update([
    'daily_conversion_cap' => 50,
    'monthly_conversion_cap' => 1000,
    'total_conversion_cap' => 10000,
    'budget_limit' => 500000, // ₦500,000
    'auto_pause_on_cap' => true,
]);

// Check cap status
$capService = app(\App\Services\OfferCapService::class);
$status = $capService->getCapStatus($offer);
```

### **3. Get Reports**

```php
$reportService = app(\App\Services\ReportingService::class);

// Get 30-day stats
$stats = $reportService->getStats([
    'date_from' => now()->subDays(30),
    'date_to' => now(),
]);

// Get daily breakdown
$dailyStats = $reportService->getDailyStats([
    'date_from' => now()->subDays(7),
    'date_to' => now(),
]);

// Top offers
$topOffers = $reportService->getTopOffers(10);

// Top affiliates
$topAffiliates = $reportService->getTopAffiliates(10);
```

### **4. Access Reports UI**

Visit these URLs after logging in:
- **Admin:** `/admin/reports`
- **Affiliate:** `/affiliate/reports`
- **Advertiser:** `/advertiser/reports`

---

## 📅 Scheduled Tasks (Recommended)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Reset daily caps at midnight
    $schedule->call(function () {
        app(\App\Services\OfferCapService::class)->resetDailyCaps();
    })->daily();

    // Reset monthly caps on first day of month
    $schedule->call(function () {
        app(\App\Services\OfferCapService::class)->resetMonthlyCaps();
    })->monthlyOn(1, '00:00');

    // Update affiliate tiers weekly
    $schedule->call(function () {
        $tierService = app(\App\Services\TierService::class);
        User::role('affiliate')->each(function ($affiliate) use ($tierService) {
            $tierService->updateAffiliateTier($affiliate);
        });
    })->weekly();
}
```

---

## 🔧 Configuration

### **Platform Settings** (Admin → Settings)

New settings to add:
- `referral_commission_percentage` - Sub-affiliate commission (default: 10%)
- Existing settings still apply (platform_fee, auto_approve, etc.)

### **Tier Customization**

Edit `TierService::$tierRequirements` to adjust:
- Minimum conversions/earnings per tier
- Bonus percentages
- Tier colors (for UI)

---

## 📈 Performance Impact

### **Before Phase 2:**
- No tier bonuses
- No cap enforcement
- Basic reporting only (clicks, conversions, revenue)
- Manual tier management

### **After Phase 2:**
- ✅ Automatic tier upgrades
- ✅ 0-15% commission bonuses
- ✅ Offer protection (caps prevent overspending)
- ✅ Advanced metrics (EPC, CR, ROI)
- ✅ Sub-affiliate referrals (10% extra earnings)
- ✅ Detailed performance analytics

---

## ✅ Checklist

### **Implemented:**
- [x] Affiliate tier system with 4 tiers
- [x] Automatic tier upgrades based on performance
- [x] Commission bonuses (0-15%)
- [x] Sub-affiliate referral system
- [x] Offer daily/monthly/lifetime caps
- [x] Budget limits and tracking
- [x] Auto-pause on cap reached
- [x] Reporting service with EPC/CR/ROI
- [x] Admin reporting dashboard route
- [x] Affiliate reporting dashboard route
- [x] Advertiser reporting dashboard route
- [x] Daily stats for charts
- [x] Top offers leaderboard
- [x] Top affiliates leaderboard
- [x] Pixel tracking (from Phase 1)
- [x] ProcessConversionJob enhancements

### **Pending (UI Pages):**
- [ ] Admin Reports/Index.vue page
- [ ] Affiliate Reports/Index.vue page
- [ ] Advertiser Reports/Index.vue page
- [ ] Navigation menu updates (add "Reports" link)
- [ ] Tier badge components
- [ ] Cap status indicators on offer pages

---

## 🎯 What's Next

### **Phase 3 Recommendations:**
1. **UI Pages** - Create Vue reporting dashboards with charts
2. **Export Functionality** - CSV/Excel export for reports
3. **Smart Links** - Link rotation with geo/device targeting
4. **Email Notifications** - Tier upgrades, cap warnings
5. **Webhook System** - Real-time conversion notifications
6. **Advanced Fraud Detection** - Quality scoring, pattern analysis
7. **A/B Testing** - Split testing for offers
8. **API Endpoints** - RESTful API for external integrations

---

## 📝 Summary

**Phase 2 Status:** ✅ **Backend Complete**

All core features implemented and functional:
- Tier system with automatic upgrades ✅
- Offer caps with auto-pause ✅
- Comprehensive reporting with advanced metrics ✅
- Enhanced conversion processing ✅

**Next Step:** Create Vue.js UI pages for the reporting dashboards.

---

**Implementation Date:** May 1, 2026  
**Status:** Backend complete, UI pages pending
