# Phase 2 Quick Reference

## 🚀 New Features at a Glance

### **Affiliate Tiers**
```
Bronze → Silver → Gold → Platinum
 0%      5%      10%     15% bonus
```

**Auto-upgrade based on:**
- Total conversions
- Lifetime earnings

---

### **Offer Caps**
Control spending with:
- Daily cap (resets at midnight)
- Monthly cap (resets on 1st)
- Total cap (lifetime)
- Budget limit (₦ amount)

**Auto-pause:** Offer stops when cap reached ✓

---

### **Reporting Metrics**

| Metric | Formula | Example |
|--------|---------|---------|
| **EPC** | Earnings / Clicks | ₦5,000 / 1,000 = ₦5 |
| **CR** | (Conversions / Clicks) × 100 | (30 / 1,000) × 100 = 3% |
| **ROI** | ((Revenue - Cost) / Cost) × 100 | ((₦10K - ₦3K) / ₦3K) × 100 = 233% |
| **EPL** | Earnings / Conversions | ₦5,000 / 30 = ₦166.67 |

---

## 💻 Quick Commands

### **Generate Referral Code**
```php
php artisan tinker

$affiliate = User::role('affiliate')->first();
app(\App\Services\TierService::class)->generateReferralCode($affiliate);
```

### **Set Offer Caps**
```php
$offer = Offer::find(1);
$offer->update([
    'daily_conversion_cap' => 50,
    'monthly_conversion_cap' => 1000,
    'budget_limit' => 500000,
]);
```

### **Get Stats**
```php
$stats = app(\App\Services\ReportingService::class)->getStats([
    'date_from' => now()->subDays(30),
    'date_to' => now(),
]);
```

---

## 📍 New Routes

| Role | URL | Purpose |
|------|-----|---------|
| **Admin** | `/admin/reports` | Platform-wide analytics |
| **Affiliate** | `/affiliate/reports` | Personal performance + tier |
| **Advertiser** | `/advertiser/reports` | Campaign analytics |

---

## 🎯 Tier Requirements

| Tier | Conversions | Earnings | Bonus |
|------|------------|----------|-------|
| Bronze | 0 | ₦0 | 0% |
| Silver | 50 | ₦5,000 | 5% |
| Gold | 200 | ₦25,000 | 10% |
| Platinum | 500 | ₦100,000 | 15% |

---

## 📊 Services Available

### **TierService**
```php
$tierService = app(\App\Services\TierService::class);

// Update tier
$tierService->updateAffiliateTier($affiliate);

// Get info
$info = $tierService->getTierInfo($affiliate);

// Leaderboard
$top = $tierService->getLeaderboard(10);
```

### **OfferCapService**
```php
$capService = app(\App\Services\OfferCapService::class);

// Check if capped
$isCapped = $capService->hasReachedCap($offer);

// Get status
$status = $capService->getCapStatus($offer);

// Reset caps
$capService->resetDailyCaps();
$capService->resetMonthlyCaps();
```

### **ReportingService**
```php
$reportService = app(\App\Services\ReportingService::class);

// Overall stats
$stats = $reportService->getStats($filters);

// Daily breakdown
$daily = $reportService->getDailyStats($filters);

// Leaderboards
$topOffers = $reportService->getTopOffers(10);
$topAffiliates = $reportService->getTopAffiliates(10);
```

---

## ⚡ Key Changes

### **ProcessConversionJob** now:
1. Checks offer caps ✓
2. Applies tier bonuses ✓
3. Updates affiliate stats ✓
4. Auto-upgrades tiers ✓
5. Processes referrals ✓
6. Increments cap counters ✓
7. Auto-pauses offers ✓

### **Commission Flow:**
```
Base Commission
    ↓
+ Tier Bonus (0-15%)
    ↓
- Platform Fee
    ↓
Apply Cap
    ↓
Final Commission
```

---

## 🔒 Scheduled Tasks Needed

Add to `app/Console/Kernel.php`:

```php
// Daily cap reset (midnight)
$schedule->call(fn() => app(\App\Services\OfferCapService::class)->resetDailyCaps())->daily();

// Monthly cap reset (1st of month)
$schedule->call(fn() => app(\App\Services\OfferCapService::class)->resetMonthlyCaps())->monthlyOn(1);

// Weekly tier updates
$schedule->call(function () {
    $tierService = app(\App\Services\TierService::class);
    User::role('affiliate')->each(fn($a) => $tierService->updateAffiliateTier($a));
})->weekly();
```

**Or run manually:**
```bash
php artisan schedule:run
```

---

## ✅ Testing

### **1. Test Tier Upgrade**
```bash
php artisan tinker

$affiliate = User::role('affiliate')->first();
$affiliate->update(['total_conversions' => 50, 'lifetime_earnings' => 5000]);
app(\App\Services\TierService::class)->updateAffiliateTier($affiliate);
$affiliate->fresh()->tier; // Should be 'silver'
```

### **2. Test Offer Cap**
```bash
$offer = Offer::first();
$offer->update(['daily_conversion_cap' => 2, 'today_conversions' => 0]);

// Process 2 conversions (should work)
// Process 3rd conversion (should be rejected)

$capService = app(\App\Services\OfferCapService::class);
$capService->hasReachedCap($offer); // true after 2nd
```

### **3. Test Reports**
```bash
$reportService = app(\App\Services\ReportingService::class);
$stats = $reportService->getStats(['date_from' => now()->subDays(7), 'date_to' => now()]);

// Check metrics
$stats['performance']['epc'];
$stats['performance']['cr'];
$stats['performance']['roi'];
```

---

## 🎉 Phase 2 Complete!

**Backend:** ✅ 100% Complete  
**Frontend UI:** ⏳ Pending (Vue pages)

**Next:** Build reporting dashboard Vue components with charts!

---

**For detailed documentation, see:** `PHASE2_IMPLEMENTATION.md`
