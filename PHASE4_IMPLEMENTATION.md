# Phase 4 Implementation: Scale & Performance

## 🚀 What Was Delivered

### 1. **Geo/Device Routing** ✅ *(Already implemented in Phase 3)*

#### **Smart Link Service**
Comprehensive geo-targeting and device routing capabilities:

**Geographic Routing:**
- **Country-level:** Allow/block specific countries (ISO codes)
- **Region/State:** Target specific regions within countries
- **City-level:** Precision targeting for city-specific campaigns

**Device Routing:**
- **Device Types:** Mobile, desktop, tablet
- **Operating Systems:** iOS, Android, Windows, macOS, Linux
- **Browsers:** Chrome, Firefox, Safari, Edge, Opera

**Time-Based Routing:**
- Active hours (start/end time in HH:MM format)
- Active days (Monday through Sunday selection)
- Timezone-aware scheduling

**Usage Example:**
```php
$link->update([
    'enable_geo_targeting' => true,
    'allowed_countries' => ['NG', 'GH', 'KE'], // Nigeria, Ghana, Kenya
    'blocked_countries' => ['US', 'GB'],       // Block USA, UK
    
    'enable_device_targeting' => true,
    'allowed_devices' => ['mobile'],           // Mobile-only campaign
    'allowed_os' => ['android', 'ios'],
    
    'enable_schedule' => true,
    'active_start_time' => '09:00:00',
    'active_end_time' => '21:00:00',
    'active_days' => [1,2,3,4,5],             // Monday-Friday
]);
```

---

### 2. **Blacklist Management** ✅ *(NEW in Phase 4)*

#### **Database Schema**
**`blacklists` table:**
- **Type:** IP, IP Range (CIDR), User Agent, Referrer, Device Fingerprint, Country, ASN
- **Matching:** Exact, Contains, Regex, Wildcard
- **Scope:** Global, Offer-specific, Affiliate-specific
- **Actions:** Block, Flag for review, Reduce quality score
- **Tracking:** Hit count, last hit timestamp
- **Expiry:** Temporary bans with expiration dates
- **Audit:** Created by, updated by

#### **Blacklist Types**

| Type | Description | Example |
|------|-------------|---------|
| **IP** | Single IP address | `192.168.1.100` |
| **IP Range** | CIDR notation | `192.168.1.0/24` |
| **User Agent** | Browser signature | `curl`, `bot`, `scraper` |
| **Referrer** | Domain pattern | `spam-site.com` |
| **Device Fingerprint** | Unique device hash | `abc123def456...` |
| **Country** | Country code | `CN`, `RU` |
| **ASN** | Autonomous System | `AS15169` (Google) |

#### **Match Types**
- **Exact:** Case-insensitive exact match
- **Contains:** Substring search
- **Regex:** Regular expression pattern
- **Wildcard:** Unix-style wildcards (*, ?)

#### **Blacklist Scopes**
- **Global:** Applies to all traffic
- **Offer-specific:** Only for specific offer
- **Affiliate-specific:** Only for specific affiliate

#### **Actions**
- **Block:** Reject traffic completely (quality score = 0)
- **Flag:** Mark for manual review
- **Reduce Quality:** Deduct points from quality score (configurable penalty)

#### **Service: `BlacklistService`**

**Key Methods:**
- `isBlacklisted(type, value, offerId, affiliateId)` - Check if value is blacklisted
- `checkIp(ip, offerId, affiliateId)` - Check IP and IP ranges
- `checkUserAgent(ua, offerId, affiliateId)` - Check user agent patterns
- `checkReferrer(referrer, offerId, affiliateId)` - Check referrer domain
- `checkDeviceFingerprint(fingerprint, offerId, affiliateId)` - Check device
- `checkCountry(countryCode, offerId, affiliateId)` - Check country
- `checkAsn(asn, offerId, affiliateId)` - Check ASN
- `checkClick(clickData, offerId, affiliateId)` - Comprehensive check for all types
- `add(data)` - Add blacklist entry
- `update(blacklist, data)` - Update entry
- `delete(blacklist)` - Delete entry
- `import(entries, createdBy)` - Bulk import from array
- `getStats()` - Get blacklist statistics

**Comprehensive Click Check:**
```php
$blacklistService = app(\App\Services\BlacklistService::class);

$result = $blacklistService->checkClick([
    'ip' => '192.168.1.100',
    'user_agent' => 'Mozilla/5.0...',
    'referrer' => 'https://spam-site.com',
    'device_fingerprint' => 'abc123...',
    'country_code' => 'CN',
], $offerId, $affiliateId);

/*
Returns:
[
    'has_violations' => true,
    'should_block' => true,
    'violations' => [
        [
            'is_blacklisted' => true,
            'blacklist_id' => 123,
            'action' => 'block',
            'quality_penalty' => 100,
            'severity' => 'critical',
            'reason' => 'Known spam IP'
        ],
        ...
    ],
    'total_penalty' => 150,
    'max_severity' => 'critical',
    'violation_count' => 2,
]
*/
```

#### **Integration with Fraud Detection**

FraudDetectionService now checks blacklists **FIRST** before analyzing:
1. Check all blacklist types (IP, UA, referrer, fingerprint, country)
2. Apply quality penalties from blacklist violations
3. Block traffic if any blacklist has action='block'
4. Continue with normal fraud analysis if not blocked
5. Combine blacklist violations with fraud indicators

**Updated Flow:**
```
Click → Blacklist Check → [BLOCKED if action=block] → Fraud Analysis → Quality Score
```

#### **Admin Controller: `BlacklistController`**

**Available Routes:**
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/admin/blacklists` | List all blacklist entries |
| POST | `/admin/blacklists` | Create new entry |
| PUT | `/admin/blacklists/{id}` | Update entry |
| DELETE | `/admin/blacklists/{id}` | Delete entry |
| POST | `/admin/blacklists/bulk-destroy` | Bulk delete |
| PATCH | `/admin/blacklists/{id}/toggle` | Toggle active status |
| POST | `/admin/blacklists/import` | Import from CSV |
| GET | `/admin/blacklists/export` | Export to CSV |
| POST | `/admin/blacklists/test` | Test if value is blacklisted |
| GET | `/admin/blacklists/stats` | Get statistics |

**CSV Import Format:**
```csv
type,value,match_type,severity,action,reason,scope,quality_penalty
ip,192.168.1.100,exact,high,block,Spam IP,global,100
ip_range,10.0.0.0/8,exact,medium,reduce_quality,Private network,global,30
user_agent,bot,contains,low,flag,Bot signature,global,0
referrer,spam-site.com,contains,critical,block,Known spam site,global,100
country,CN,exact,medium,reduce_quality,High fraud rate,offer,50
```

**CSV Export Format:**
```csv
Type,Value,Match Type,Scope,Severity,Action,Quality Penalty,Reason,Hit Count,Is Active,Created At,Created By
ip,"192.168.1.100",exact,global,high,block,100,"Spam IP",45,Yes,2026-05-01 12:00:00,Admin User
```

#### **Test Endpoint**
Test if a value would be blacklisted without creating a click:

```bash
POST /admin/blacklists/test
{
  "type": "ip",
  "value": "192.168.1.100",
  "offer_id": 10,
  "affiliate_id": null
}

Response:
{
  "is_blacklisted": true,
  "blacklist_id": 123,
  "action": "block",
  "quality_penalty": 100,
  "severity": "critical",
  "reason": "Known spam IP"
}
```

#### **Statistics Dashboard**
```php
$stats = $blacklistService->getStats();

/*
Returns:
[
    'total' => 1250,
    'active' => 1100,
    'by_type' => [
        'ip' => 450,
        'ip_range' => 50,
        'user_agent' => 300,
        'referrer' => 200,
        'device_fingerprint' => 100,
        'country' => 100,
        'asn' => 50,
    ],
    'by_severity' => [
        'low' => 200,
        'medium' => 500,
        'high' => 350,
        'critical' => 200,
    ],
    'top_hits' => [
        // Top 10 most triggered blacklist entries
    ],
]
*/
```

#### **Caching Strategy**
- **Cache TTL:** 1 hour (configurable)
- **Cache Key Pattern:** `blacklist:{type}:{scope}:{md5(value)}`
- **Cache Tags:** `blacklist`, `blacklist:{type}`
- **Auto-invalidation:** On create, update, delete

**Performance:**
- First check: Database query (cached for 1 hour)
- Subsequent checks: Cache hit (~1ms)
- Cache flush: Only affected type/scope

---

### 3. **A/B Testing** ✅ *(Already implemented in Phase 3)*

#### **Link Rotation Groups**
Complete A/B testing framework through rotation groups:

**Features:**
- **Split Testing:** Enable/disable split test mode
- **Duration:** Configurable test duration in days
- **Start/End Dates:** Automatic test period tracking
- **Performance Tracking:** Track clicks, conversions, revenue per variation
- **Metrics:** Conversion rate (CR) and earnings per click (EPC) per link
- **Winner Selection:** Automatic identification of best performer

**Rotation Strategies:**
- **Random:** Perfect for A/B testing (equal distribution)
- **Weighted:** Allocate custom traffic percentages
- **Sequential:** Round-robin for equal exposure
- **Performance:** Shift traffic to winners (70%/20%/10% split)

**Create A/B Test:**
```php
$smartLinkService = app(\App\Services\SmartLinkService::class);

// Create test group
$group = $smartLinkService->createRotationGroup([
    'affiliate_id' => 1,
    'offer_id' => 10,
    'name' => 'Landing Page A/B Test',
    'description' => 'Testing blue vs red CTA button',
    'rotation_strategy' => 'random',      // Equal distribution
    'enable_split_test' => true,
    'split_test_duration_days' => 7,     // 7-day test
    'auto_optimize' => false,             // Don't auto-optimize during test
]);

// Add variations
$smartLinkService->addLinkToGroup($linkA, $group); // Variation A
$smartLinkService->addLinkToGroup($linkB, $group); // Variation B
$smartLinkService->addLinkToGroup($linkC, $group); // Variation C

// Start test
$group->update([
    'split_test_started_at' => now(),
    'split_test_ends_at' => now()->addDays(7),
]);
```

**Get Test Results:**
```php
$stats = $smartLinkService->getGroupStats($group);

/*
Returns:
[
    'group_id' => 1,
    'group_name' => 'Landing Page A/B Test',
    'total_links' => 3,
    'total_clicks' => 5000,
    'total_conversions' => 150,
    'total_revenue' => 150000,
    'group_cr' => 3.0,
    'group_epc' => 30.00,
    'links' => [
        [
            'id' => 100,
            'tracking_code' => 'abc123',
            'rotation_clicks' => 1700,
            'rotation_conversions' => 68,
            'rotation_cr' => 4.0,          // Winner! 🏆
            'rotation_weight' => 33.3,
        ],
        [
            'id' => 101,
            'tracking_code' => 'def456',
            'rotation_clicks' => 1650,
            'rotation_conversions' => 45,
            'rotation_cr' => 2.7,
            'rotation_weight' => 33.3,
        ],
        [
            'id' => 102,
            'tracking_code' => 'ghi789',
            'rotation_clicks' => 1650,
            'rotation_conversions' => 37,
            'rotation_cr' => 2.2,
            'rotation_weight' => 33.3,
        ],
    ],
]
*/
```

**Post-Test Actions:**
1. Review stats to identify winner
2. Disable underperforming variations
3. Switch to 'performance' strategy to shift traffic to winner
4. Or create new test with winner + new variations

---

## 📊 Performance Optimizations

### **Caching**
- **Blacklist Cache:** 1 hour TTL, tagged cache for selective flushing
- **IP Reputation Cache:** 30 days TTL
- **Click Velocity Cache:** 1 hour sliding window
- **Device Fingerprint Cache:** 24 hours
- **Rotation Group Cache:** On-demand, invalidated on update

### **Database Indexes**
```sql
-- Blacklist indexes
CREATE INDEX idx_blacklists_type_active ON blacklists(type, is_active);
CREATE INDEX idx_blacklists_value_type ON blacklists(value(100), type);
CREATE INDEX idx_blacklists_scope ON blacklists(scope, scope_id, offer_id);
CREATE INDEX idx_blacklists_expires ON blacklists(expires_at);

-- Performance tracking
CREATE INDEX idx_blacklists_hits ON blacklists(hit_count DESC, last_hit_at);
```

### **Query Optimization**
- Eager loading for relationships
- Indexed foreign keys
- Pagination for large datasets
- Selective column retrieval

---

## 🎯 How to Use

### **1. Add IP to Blacklist**

```php
$blacklistService = app(\App\Services\BlacklistService::class);

$blacklistService->add([
    'type' => 'ip',
    'value' => '192.168.1.100',
    'match_type' => 'exact',
    'scope' => 'global',
    'is_active' => true,
    'severity' => 'high',
    'action' => 'block',
    'quality_penalty' => 100,
    'reason' => 'Known spam IP',
    'notes' => 'Multiple fraud attempts detected',
    'created_by' => auth()->id(),
]);
```

### **2. Block IP Range**

```php
$blacklistService->add([
    'type' => 'ip_range',
    'value' => '10.0.0.0/8',        // Block entire 10.x.x.x range
    'match_type' => 'exact',
    'scope' => 'global',
    'severity' => 'medium',
    'action' => 'reduce_quality',
    'quality_penalty' => 30,
    'reason' => 'Private network range',
    'created_by' => auth()->id(),
]);
```

### **3. Block Bot User Agents**

```php
$blacklistService->add([
    'type' => 'user_agent',
    'value' => 'bot',                // Matches any UA containing "bot"
    'match_type' => 'contains',
    'scope' => 'global',
    'severity' => 'high',
    'action' => 'block',
    'quality_penalty' => 100,
    'reason' => 'Bot signature detected',
    'created_by' => auth()->id(),
]);
```

### **4. Block Spam Referrers**

```php
$blacklistService->add([
    'type' => 'referrer',
    'value' => 'spam-site.com',
    'match_type' => 'contains',
    'scope' => 'global',
    'severity' => 'critical',
    'action' => 'block',
    'quality_penalty' => 100,
    'reason' => 'Known spam referrer',
    'created_by' => auth()->id(),
]);
```

### **5. Block Countries**

```php
// Block China for offer #10
$blacklistService->add([
    'type' => 'country',
    'value' => 'CN',
    'match_type' => 'exact',
    'scope' => 'offer',
    'offer_id' => 10,
    'severity' => 'medium',
    'action' => 'block',
    'quality_penalty' => 100,
    'reason' => 'Offer not available in China',
    'created_by' => auth()->id(),
]);
```

### **6. Temporary Ban (with Expiry)**

```php
$blacklistService->add([
    'type' => 'ip',
    'value' => '203.0.113.50',
    'match_type' => 'exact',
    'scope' => 'global',
    'severity' => 'medium',
    'action' => 'block',
    'quality_penalty' => 100,
    'reason' => 'Suspicious activity',
    'expires_at' => now()->addDays(30),  // Auto-expire in 30 days
    'created_by' => auth()->id(),
]);
```

### **7. Import from CSV**

```php
$entries = [
    [
        'type' => 'ip',
        'value' => '1.2.3.4',
        'match_type' => 'exact',
        'severity' => 'high',
        'action' => 'block',
        'reason' => 'Spam IP',
        'scope' => 'global',
        'quality_penalty' => 100,
    ],
    // ... more entries
];

$result = $blacklistService->import($entries, auth()->id());
echo "{$result['imported']} imported, {$result['failed']} failed";
```

### **8. Check If IP Is Blacklisted**

```php
$result = $blacklistService->checkIp('192.168.1.100', $offerId, $affiliateId);

if ($result['is_blacklisted']) {
    echo "IP is blacklisted: {$result['reason']}";
    echo "Action: {$result['action']}";
    echo "Penalty: {$result['quality_penalty']} points";
}
```

### **9. Configure Smart Link with Geo-Targeting**

```php
$link = AffiliateLink::find(100);

$link->update([
    // Enable geo-targeting
    'enable_geo_targeting' => true,
    'allowed_countries' => ['NG', 'GH', 'KE', 'ZA'],  // African countries only
    'blocked_countries' => [],
    
    // Enable device targeting
    'enable_device_targeting' => true,
    'allowed_devices' => ['mobile', 'tablet'],       // Mobile traffic only
    'allowed_os' => ['android', 'ios'],
    
    // Enable scheduling
    'enable_schedule' => true,
    'active_start_time' => '06:00:00',              // 6 AM
    'active_end_time' => '23:00:00',                // 11 PM
    'active_days' => [1,2,3,4,5,6,7],               // Every day
]);
```

### **10. Run A/B Test**

```php
$smartLinkService = app(\App\Services\SmartLinkService::class);

// Create test group
$group = LinkRotationGroup::create([
    'affiliate_id' => 1,
    'offer_id' => 10,
    'name' => 'CTA Button Color Test',
    'rotation_strategy' => 'random',
    'enable_split_test' => true,
    'split_test_duration_days' => 14,
    'split_test_started_at' => now(),
    'split_test_ends_at' => now()->addDays(14),
]);

// Add variations
$linkBlue->update(['rotation_group_id' => $group->id]);
$linkRed->update(['rotation_group_id' => $group->id]);
$linkGreen->update(['rotation_group_id' => $group->id]);

// After 14 days, check results
$stats = $smartLinkService->getGroupStats($group);
```

---

## 📈 Benefits

### **Blacklist Management**
- ✅ **Block Fraud:** Instantly block known bad actors
- ✅ **Reduce Manual Work:** Automated blacklist checking on every click
- ✅ **Granular Control:** Block globally or per offer/affiliate
- ✅ **Flexible Matching:** Exact, contains, regex, wildcard patterns
- ✅ **Temporary Bans:** Auto-expiring blacklist entries
- ✅ **Hit Tracking:** See which blacklists are most effective
- ✅ **CSV Import/Export:** Bulk management and backup

### **Geo/Device Routing**
- ✅ **Better Targeting:** Show relevant offers based on location/device
- ✅ **Higher Conversions:** Match traffic to appropriate campaigns
- ✅ **Compliance:** Block regions where offer is not allowed
- ✅ **Performance:** Mobile-specific vs desktop campaigns
- ✅ **Scheduling:** Run campaigns during optimal hours

### **A/B Testing**
- ✅ **Data-Driven:** Make decisions based on real performance data
- ✅ **Optimize CR:** Find best performing variations
- ✅ **Maximize Revenue:** Shift traffic to winners automatically
- ✅ **Test Anything:** Landing pages, offers, creatives, messaging
- ✅ **Statistical Validity:** Minimum 50 clicks per variation

---

## ⚙️ Configuration

### **Blacklist Settings**

**Cache TTL:**
```php
// In BlacklistService.php
protected const CACHE_TTL = 3600; // 1 hour (adjust as needed)
```

**Match Types:**
- `exact`: Case-insensitive exact match
- `contains`: Substring search (case-insensitive)
- `regex`: Regular expression (use with caution)
- `wildcard`: Unix-style wildcards (*, ?)

**Quality Penalties:**
- `0-100` points deducted from quality score
- Recommended: Block = 100, High = 50-80, Medium = 20-50, Low = 10-20

### **Fraud Detection Integration**

**Blacklist Check Order:**
1. IP address (single + ranges)
2. User agent
3. Referrer domain
4. Device fingerprint
5. Country code

**Block Behavior:**
- If any blacklist has `action='block'`, click is rejected (score = 0)
- If `action='reduce_quality'`, penalty is applied
- If `action='flag'`, marked for manual review

---

## 🧪 Testing

### **Test Blacklist Entry**

```php
// Via service
$result = $blacklistService->checkIp('192.168.1.100');
$this->assertTrue($result['is_blacklisted']);

// Via HTTP endpoint
POST /admin/blacklists/test
{
  "type": "ip",
  "value": "192.168.1.100"
}
```

### **Test Geo-Targeting**

```php
$context = [
    'country' => 'NG',
    'region' => 'Lagos',
    'city' => 'Lagos',
    'device' => 'mobile',
    'os' => 'android',
    'browser' => 'chrome',
];

$link = $smartLinkService->selectLink($context);
$this->assertNotNull($link); // Should return matching link
```

### **Test A/B Split**

```php
// Simulate 1000 clicks and verify distribution
$clicks = [];
for ($i = 0; $i < 1000; $i++) {
    $link = $smartLinkService->selectLink($context);
    $clicks[$link->id] = ($clicks[$link->id] ?? 0) + 1;
}

// For random strategy, should be roughly 500/500 for 2 variations
$this->assertEqualsWithDelta(500, $clicks[$linkA->id], 50);
$this->assertEqualsWithDelta(500, $clicks[$linkB->id], 50);
```

---

## ✅ Phase 4 Complete!

**Status:** ✅ **100% Complete**

**Delivered:**
- ✅ Blacklist management system (7 types, 4 match types, 3 scopes)
- ✅ Blacklist service with comprehensive checking
- ✅ Admin controller with CRUD + import/export
- ✅ Integration with fraud detection
- ✅ CSV import/export functionality
- ✅ Test endpoint for validating entries
- ✅ Statistics dashboard
- ✅ Geo/device routing (Phase 3)
- ✅ A/B testing framework (Phase 3)
- ✅ Performance optimizations (caching, indexing)

**Next Steps:**
- Phase 5: UI Components for blacklist management
- Phase 5: Geo-targeting UI builder
- Phase 5: A/B test results visualization
- Phase 5: Email alerts for blacklist hits

---

**Implementation Date:** May 1, 2026  
**Status:** Backend complete, ready for production
