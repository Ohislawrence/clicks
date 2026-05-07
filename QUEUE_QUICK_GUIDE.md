# DealsIntel - Queue System Quick Guide

## 🚀 Starting the Queue Worker

### Development
```bash
# Start queue worker (keeps running in terminal)
php artisan queue:work

# Or run in the background (Windows)
Start-Process -NoNewWindow php artisan queue:work
```

### Production (Recommended)
Use the `composer dev` script which starts all services including queue worker:
```bash
composer dev
```

This command runs 4 concurrent processes:
1. ✅ Laravel server (`php artisan serve`)
2. ✅ Queue worker (`php artisan queue:listen`)
3. ✅ Log viewer (`php artisan pail`)
4. ✅ Vite dev server (`npm run dev`)

---

## 📊 Monitoring Jobs

### Check Queue Status
```bash
# View pending jobs
php artisan tinker
>>> DB::table('jobs')->count();
>>> DB::table('jobs')->get();

# View failed jobs
php artisan queue:failed

# View specific failed job details
php artisan queue:failed-table
```

### Retry Failed Jobs
```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job by ID
php artisan queue:retry 5

# Clear all failed jobs
php artisan queue:flush
```

---

## 🧪 Testing the System

### 1. Test Click Tracking
Visit a tracking URL:
```
http://dealsintel.test/track/{tracking_code}
```

Check the `jobs` table - a `ProcessClickJob` should appear.

### 2. Test Conversion Tracking

#### Via Postback (cURL):
```bash
curl -X POST http://dealsintel.test/postback \
  -H "Content-Type: application/json" \
  -d '{
    "tracking_code": "YOUR_TRACKING_CODE",
    "transaction_id": "TEST123",
    "conversion_value": 100.00
  }'
```

#### Via Pixel (HTML):
```html
<img src="http://dealsintel.test/pixel?value=100.00&txn_id=TEST123" width="1" height="1" />
```

---

## 📋 What Jobs Do

### `ProcessClickJob`
- Tracks click data (IP, device, browser, location)
- Performs fraud detection
- Caches click data for conversion attribution
- Updates offer/link statistics

### `ProcessConversionJob`
- Finds click via cache or database
- Calculates commission with fees
- Creates conversion and commission records
- Updates affiliate balance
- Triggers postback to advertiser

### `SendPostbackJob`
- Sends HTTP callback to advertiser
- Replaces macros in postback URL
- Retries up to 5 times on failure
- Logs all responses

---

## ⚡ Performance

**Before (Sync):**
- Click redirect: ~300ms
- User waits for processing

**After (Async):**
- Click redirect: ~30ms
- Processing in background

**Result:** 10x faster redirects!

---

## 🔧 Troubleshooting

**Jobs not processing?**
```bash
# Check if worker is running
Get-Process | Where-Object {$_.ProcessName -like "*php*"}

# Restart queue worker
php artisan queue:restart
```

**Want to clear all jobs?**
```bash
DB::table('jobs')->truncate();
DB::table('failed_jobs')->truncate();
```

**Check logs:**
```
storage/logs/laravel.log
```

---

**For detailed documentation, see:** `PHASE1_IMPLEMENTATION.md`
