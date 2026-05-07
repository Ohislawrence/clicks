# Cron Jobs Setup for ClicksIntel

## Current Configuration

Your cron jobs are correctly configured for production. Here's the optimal setup:

### 1. Queue Worker (Every Minute)
```bash
* * * * * /usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan queue:work --stop-when-empty --max-time=50 --tries=3 >> /home/boomcvmo/clicksintel.com/storage/logs/queue-worker.log 2>&1
```

**What it does:**
- Processes all queued jobs (ProcessPayoutJob, ProcessImageJob, ProcessBulkConversionActionJob, notifications)
- Stops when queue is empty (prevents memory leaks)
- Max 50 seconds per run (safe for 1-minute cron interval)
- 3 retry attempts for failed jobs
- Logs to queue-worker.log for debugging

### 2. Laravel Scheduler (Every Minute)
```bash
* * * * * /usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan schedule:run >> /home/boomcvmo/clicksintel.com/storage/logs/scheduler.log 2>&1
```

**What it does:**
- Runs SendWeeklyPerformanceSummaries (weekly)
- Any other scheduled tasks defined in `app/Console/Kernel.php`
- Logs to scheduler.log

---

## Recommended Cron Configuration

Add these two lines to your crontab (`crontab -e`):

```cron
# Laravel Queue Worker - Process background jobs
* * * * * /usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan queue:work --stop-when-empty --max-time=50 --tries=3 >> /home/boomcvmo/clicksintel.com/storage/logs/queue-worker.log 2>&1

# Laravel Scheduler - Run scheduled tasks
* * * * * /usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan schedule:run >> /home/boomcvmo/clicksintel.com/storage/logs/scheduler.log 2>&1
```

---

## Why `--stop-when-empty` is Perfect for Cron

✅ **Your current setup is CORRECT** for shared hosting because:

1. **Prevents zombie processes** - Worker stops after processing all jobs
2. **Efficient** - Runs every minute, processes everything, then exits
3. **Resource-friendly** - No long-running processes consuming memory
4. **Reliable** - If a worker crashes, a new one starts next minute

---

## Alternative: Supervisor (For VPS/Dedicated)

If you have root access, Supervisor is more efficient:

```ini
[program:clicksintel-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=boomcvmo
numprocs=2
redirect_stderr=true
stdout_logfile=/home/boomcvmo/clicksintel.com/storage/logs/worker.log
stopwaitsecs=3600
```

Then only keep scheduler in cron:
```cron
* * * * * /usr/local/bin/php /home/boomcvmo/clicksintel.com/artisan schedule:run >> /dev/null 2>&1
```

---

## Monitoring Queue Health

### Check if jobs are processing:
```bash
php artisan queue:monitor
```

### View failed jobs:
```bash
php artisan queue:failed
```

### Retry failed jobs:
```bash
php artisan queue:retry all
```

### Clear failed jobs:
```bash
php artisan queue:flush
```

---

## Current Jobs Being Processed

Your cron will automatically handle these jobs:

| Job | Purpose | Timeout | Retries |
|-----|---------|---------|---------|
| **ProcessPayoutJob** | Payment gateway API calls (Paystack/Flutterwave) | 120s | 3 |
| **ProcessImageJob** | Image optimization & thumbnails | 120s | 2 |
| **ProcessBulkConversionActionJob** | Bulk approve/reject conversions | 300s | 2 |
| **ProcessClickJob** | Click tracking & fraud detection | 60s | 3 |
| **ProcessConversionJob** | Conversion processing & commissions | 120s | 3 |
| **SendPostbackJob** | Advertiser HTTP callbacks | 60s | 5 |
| **All Notifications** | Email notifications (26 types) | Default | 3 |

---

## Troubleshooting

### Jobs not processing?
```bash
# Check queue table
mysql -u [user] -p[pass] [database] -e "SELECT * FROM jobs LIMIT 10;"

# Check logs
tail -f /home/boomcvmo/clicksintel.com/storage/logs/laravel.log
tail -f /home/boomcvmo/clicksintel.com/storage/logs/queue-worker.log
```

### Queue backing up?
```bash
# Count pending jobs
mysql -u [user] -p[pass] [database] -e "SELECT COUNT(*) FROM jobs;"

# If too many, increase cron frequency to every 30 seconds:
# * * * * * sleep 30; /usr/local/bin/php ... queue:work ...
```

### Check cron is running:
```bash
# View cron logs
grep CRON /var/log/syslog
```

---

## Performance Tips

1. **Database Indexing** - Ensure `jobs` table has indexes:
   ```sql
   CREATE INDEX jobs_queue_index ON jobs(queue);
   CREATE INDEX jobs_available_at_index ON jobs(available_at);
   ```

2. **Failed Jobs Cleanup** - Add to scheduler in `app/Console/Kernel.php`:
   ```php
   $schedule->command('queue:prune-failed --hours=168')->weekly();
   ```

3. **Log Rotation** - Prevent logs from growing too large:
   ```bash
   # Add to logrotate
   /home/boomcvmo/clicksintel.com/storage/logs/*.log {
       daily
       missingok
       rotate 14
       compress
       notifempty
   }
   ```

---

## ✅ Your Setup Status

- ✅ Queue worker cron configured
- ✅ Scheduler cron configured
- ✅ Using `--stop-when-empty` (perfect for shared hosting)
- ✅ Jobs will process every minute
- ✅ All 7 queue optimization jobs ready

**Your cron configuration is production-ready!** 🚀
