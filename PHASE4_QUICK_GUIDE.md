# Phase 4 Quick Reference

## 🚫 Blacklist Management

### **Quick Add**
```php
$blacklistService = app(\App\Services\BlacklistService::class);

// Block IP
$blacklistService->add([
    'type' => 'ip',
    'value' => '192.168.1.100',
    'match_type' => 'exact',
    'scope' => 'global',
    'action' => 'block',
    'severity' => 'high',
    'quality_penalty' => 100,
    'reason' => 'Spam IP',
    'created_by' => auth()->id(),
]);
```

### **Blacklist Types**
| Type | Example | Match Type |
|------|---------|------------|
| `ip` | `192.168.1.100` | exact |
| `ip_range` | `10.0.0.0/8` | exact (CIDR) |
| `user_agent` | `bot`, `curl` | contains |
| `referrer` | `spam-site.com` | contains |
| `device_fingerprint` | `abc123...` | exact |
| `country` | `CN`, `RU` | exact (ISO) |
| `asn` | `AS15169` | exact |

### **Actions**
- `block` - Reject completely (score = 0)
- `reduce_quality` - Deduct penalty points
- `flag` - Mark for manual review

### **Scopes**
- `global` - All traffic
- `offer` - Specific offer (set `offer_id`)
- `affiliate` - Specific affiliate (set `scope_id`)

### **Quick Check**
```php
// Check if IP is blacklisted
$result = $blacklistService->checkIp('192.168.1.100', $offerId, $affiliateId);

if ($result['is_blacklisted']) {
    echo "Blacklisted: {$result['reason']}";
}
```

### **Comprehensive Check**
```php
$result = $blacklistService->checkClick([
    'ip' => '192.168.1.100',
    'user_agent' => 'curl/7.68.0',
    'referrer' => 'https://spam-site.com',
    'device_fingerprint' => 'abc123',
    'country_code' => 'CN',
], $offerId, $affiliateId);

echo "Violations: {$result['violation_count']}";
echo "Should block: " . ($result['should_block'] ? 'YES' : 'NO');
echo "Total penalty: {$result['total_penalty']} points";
```

---

## 🌍 Geo/Device Routing

### **Setup Geo-Targeting**
```php
$link->update([
    'enable_geo_targeting' => true,
    'allowed_countries' => ['NG', 'GH', 'KE'],  // Allow
    'blocked_countries' => ['US', 'GB'],         // Block
    'allowed_regions' => ['Lagos', 'Accra'],
    'allowed_cities' => ['Lagos', 'Abuja'],
]);
```

### **Setup Device Targeting**
```php
$link->update([
    'enable_device_targeting' => true,
    'allowed_devices' => ['mobile'],             // Mobile only
    'allowed_os' => ['android', 'ios'],
    'allowed_browsers' => ['chrome', 'safari'],
]);
```

### **Setup Time-Based**
```php
$link->update([
    'enable_schedule' => true,
    'active_start_time' => '09:00:00',
    'active_end_time' => '17:00:00',
    'active_days' => [1,2,3,4,5],               // Mon-Fri
]);
```

### **Select Best Link**
```php
$smartLinkService = app(\App\Services\SmartLinkService::class);

$link = $smartLinkService->selectLink([
    'country' => 'NG',
    'region' => 'Lagos',
    'city' => 'Lagos',
    'device' => 'mobile',
    'os' => 'android',
    'browser' => 'chrome',
]);
```

---

## 🧪 A/B Testing

### **Create Test**
```php
$group = LinkRotationGroup::create([
    'affiliate_id' => 1,
    'offer_id' => 10,
    'name' => 'Landing Page Test',
    'rotation_strategy' => 'random',             // Equal split
    'enable_split_test' => true,
    'split_test_duration_days' => 7,
    'split_test_started_at' => now(),
    'split_test_ends_at' => now()->addDays(7),
]);

// Add variations
$linkA->update(['rotation_group_id' => $group->id]);
$linkB->update(['rotation_group_id' => $group->id]);
```

### **Rotation Strategies**
| Strategy | Distribution | Use Case |
|----------|--------------|----------|
| `random` | Equal random | A/B testing |
| `sequential` | Round-robin | Equal exposure |
| `weighted` | By weight % | Custom split |
| `performance` | 70/20/10 | Winner takes most |

### **Get Results**
```php
$stats = $smartLinkService->getGroupStats($group);

echo "Total clicks: {$stats['total_clicks']}";
echo "Total conversions: {$stats['total_conversions']}";
echo "Group CR: {$stats['group_cr']}%";

foreach ($stats['links'] as $link) {
    echo "Link {$link['id']}: {$link['rotation_cr']}% CR";
}
```

---

## 🔄 Admin Routes

### **Blacklist Routes**
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/admin/blacklists` | List entries |
| POST | `/admin/blacklists` | Create entry |
| PUT | `/admin/blacklists/{id}` | Update entry |
| DELETE | `/admin/blacklists/{id}` | Delete entry |
| POST | `/admin/blacklists/bulk-destroy` | Bulk delete |
| PATCH | `/admin/blacklists/{id}/toggle` | Toggle active |
| POST | `/admin/blacklists/import` | Import CSV |
| GET | `/admin/blacklists/export` | Export CSV |
| POST | `/admin/blacklists/test` | Test value |
| GET | `/admin/blacklists/stats` | Statistics |

---

## 📤 Import/Export

### **CSV Import Format**
```csv
type,value,match_type,severity,action,reason,scope,quality_penalty
ip,192.168.1.100,exact,high,block,Spam IP,global,100
ip_range,10.0.0.0/8,exact,medium,reduce_quality,Private,global,30
user_agent,bot,contains,low,flag,Bot,global,0
```

### **Import via Code**
```php
$entries = [
    [
        'type' => 'ip',
        'value' => '1.2.3.4',
        'match_type' => 'exact',
        'severity' => 'high',
        'action' => 'block',
        'reason' => 'Spam',
        'scope' => 'global',
        'quality_penalty' => 100,
    ],
];

$result = $blacklistService->import($entries, auth()->id());
```

### **Export via URL**
```bash
GET /admin/blacklists/export?type=ip&severity=high
```

---

## 📊 Statistics

### **Get Blacklist Stats**
```php
$stats = $blacklistService->getStats();

echo "Total: {$stats['total']}";
echo "Active: {$stats['active']}";
print_r($stats['by_type']);
print_r($stats['by_severity']);
print_r($stats['top_hits']);
```

---

## 🧩 Integration

### **Fraud Detection Flow**
```
Click → Blacklist Check → [Block?] → Fraud Analysis → Quality Score
```

**In FraudDetectionService:**
1. Checks all blacklist types first
2. Blocks if `action='block'`
3. Applies quality penalties
4. Continues with normal fraud analysis
5. Combines blacklist + fraud indicators

### **Auto-Optimization**
```php
// Enable auto-optimization for rotation group
$group->update([
    'auto_optimize' => true,
    'optimization_threshold_clicks' => 500,
]);

// When threshold reached:
// - Disables links with CR < 50% of average
// - Requires minimum 50 clicks per link
```

---

## ⚙️ Configuration

### **Blacklist Cache**
```php
// In BlacklistService.php
protected const CACHE_TTL = 3600; // 1 hour
```

### **Quality Penalties**
- Critical: 80-100 points
- High: 50-80 points
- Medium: 20-50 points
- Low: 10-20 points

### **Match Types**
- `exact`: Exact match (case-insensitive)
- `contains`: Substring search
- `regex`: Regular expression
- `wildcard`: Unix wildcards (*, ?)

---

## 🎯 Quick Commands

### **Test Blacklist**
```bash
POST /admin/blacklists/test
{
  "type": "ip",
  "value": "192.168.1.100"
}
```

### **Block Country**
```php
$blacklistService->add([
    'type' => 'country',
    'value' => 'CN',
    'match_type' => 'exact',
    'scope' => 'global',
    'action' => 'block',
    'severity' => 'medium',
    'quality_penalty' => 100,
    'reason' => 'High fraud rate',
    'created_by' => auth()->id(),
]);
```

### **Temporary Ban**
```php
$blacklistService->add([
    'type' => 'ip',
    'value' => '203.0.113.50',
    'match_type' => 'exact',
    'scope' => 'global',
    'action' => 'block',
    'severity' => 'medium',
    'quality_penalty' => 100,
    'reason' => 'Suspicious',
    'expires_at' => now()->addDays(30),  // Auto-expire
    'created_by' => auth()->id(),
]);
```

---

## ✅ Phase 4 Complete!

**Blacklist Management:** ✅ 7 types + 4 match types  
**Geo/Device Routing:** ✅ Country/region/city + device/OS/browser  
**A/B Testing:** ✅ Split testing + 4 rotation strategies  
**Performance:** ✅ Caching + indexing + optimization  

**Next:** Build UI components & dashboards

---

**For full documentation, see:** `PHASE4_IMPLEMENTATION.md`
