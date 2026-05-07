# Phase 3 Quick Reference

## 🎯 Smart Links

### **Rotation Strategies**
```php
'sequential'   // Round-robin (equal distribution)
'weighted'     // Custom % allocation via rotation_weight
'random'       // Completely random
'performance'  // Best performer gets 70% traffic
```

### **Quick Setup**
```php
$service = app(\App\Services\SmartLinkService::class);

// Create group
$group = $service->createRotationGroup([
    'affiliate_id' => 1,
    'offer_id' => 10,
    'name' => 'Test Group',
    'rotation_strategy' => 'performance',
    'auto_optimize' => true,
]);

// Add links
$service->addLinkToGroup($link1, $group);
$service->addLinkToGroup($link2, $group);
```

### **Targeting Options**

**Geo:**
```php
$link->update([
    'enable_geo_targeting' => true,
    'allowed_countries' => ['NG', 'GH'],
    'blocked_countries' => ['US'],
]);
```

**Device:**
```php
$link->update([
    'enable_device_targeting' => true,
    'allowed_devices' => ['mobile'],
    'allowed_os' => ['android', 'ios'],
]);
```

**Schedule:**
```php
$link->update([
    'enable_schedule' => true,
    'active_start_time' => '09:00',
    'active_end_time' => '17:00',
    'active_days' => [1,2,3,4,5], // Mon-Fri
]);
```

---

## 🔍 Fraud Detection

### **Quality Score Ranges**
| Score | Quality | Action |
|-------|---------|--------|
| 80-100 | Excellent | ✅ Accept |
| 60-79 | Good | ✅ Accept |
| 40-59 | Fair | ⚠️ Monitor |
| 0-39 | Poor | ❌ Block |

### **Risk Levels**
- **Low:** Trustworthy traffic
- **Medium:** Acceptable quality
- **High:** Requires review
- **Critical:** Likely fraud

### **Fraud Indicators Detected**
✅ Bot signatures  
✅ High click velocity  
✅ Uniform patterns  
✅ VPN/Proxy usage  
✅ Low IP reputation  
✅ Device fingerprint abuse  
✅ Suspicious referrers  
✅ Too-fast conversions  
✅ Duplicate transactions  

### **Get Fraud Stats**
```php
$stats = app(\App\Services\FraudDetectionService::class)
    ->getFraudStats(now()->subDays(30), now());

echo $stats['fraud_rate']; // e.g., 8.5%
echo $stats['average_quality_score']; // e.g., 72.3
```

---

## 📤 Export Functionality

### **Available Exports**
| Export | URL |
|--------|-----|
| Stats | `/admin/reports/export/stats` |
| Daily Stats | `/admin/reports/export/daily-stats` |
| Clicks | `/admin/reports/export/clicks` |
| Conversions | `/admin/reports/export/conversions` |

### **Query Parameters**
- `date_from` - Start date (Y-m-d)
- `date_to` - End date (Y-m-d)
- `offer_id` - Filter by offer
- `status` - Filter by status

### **Export via Code**
```php
$exportService = app(\App\Services\ExportService::class);

$csv = $exportService->exportClicks([
    'date_from' => '2026-04-01',
    'date_to' => '2026-05-01',
]);

return response($csv, 200)
    ->header('Content-Type', 'text/csv')
    ->header('Content-Disposition', 'attachment; filename=export.csv');
```

---

## 🎲 Sub-Affiliate Referrals

*(Implemented in Phase 2)*

```php
$tierService = app(\App\Services\TierService::class);

// Generate code
$code = $tierService->generateReferralCode($affiliate);

// Process referral (10% default)
$tierService->processReferral($parent, $child, $commission);
```

---

## 📊 Key Metrics

### **Smart Link Performance**
- `rotation_clicks` - Total clicks through rotation
- `rotation_conversions` - Conversions from rotation
- `rotation_cr` - Conversion rate %
- `group_epc` - Earnings per click

### **Fraud Metrics**
- `quality_score` - 0-100 quality rating
- `risk_level` - low/medium/high/critical
- `fraud_indicators` - Array of detected issues
- `needs_manual_review` - Boolean flag

---

## ⚡ Quick Commands

### **View Rotation Group**
```php
$stats = app(\App\Services\SmartLinkService::class)
    ->getGroupStats($group);

print_r($stats);
```

### **Check Click Quality**
```php
$click = Click::find(123);
echo "Quality: {$click->quality_score}\n";
echo "Risk: {$click->risk_level}\n";
print_r($click->fraud_indicators);
```

### **Export Report**
```bash
# Visit in browser or curl
curl "https://dealsintel.com/admin/reports/export/stats?date_from=2026-04-01&date_to=2026-05-01" > stats.csv
```

---

## 🔧 Configuration

### **Fraud Thresholds**
Edit `FraudDetectionService.php`:
```php
const QUALITY_EXCELLENT = 80;
const QUALITY_GOOD = 60;
const QUALITY_FAIR = 40;
const QUALITY_POOR = 20;
```

### **Click Validity**
Edit `ProcessClickJob.php`:
```php
'is_valid' => $fraudAnalysis['quality_score'] >= 40
```

### **Auto-Optimization**
- Min clicks per link: 50
- Underperformance: CR < 50% of average
- Trigger: Group reaches threshold

---

## ✅ Phase 3 Complete!

**Smart Links:** ✅ 4 strategies + targeting  
**Fraud Detection:** ✅ 13+ indicators + quality score  
**Export:** ✅ 5 export types (CSV)  
**Referrals:** ✅ 10% commission (Phase 2)  

**Next:** Build UI components & dashboards

---

**For full documentation, see:** `PHASE3_IMPLEMENTATION.md`
