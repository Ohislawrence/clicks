# Store Builder - Phase 6 Complete
## Jobs & Notifications for Subscription Lifecycle Management

**Status:** ✅ Complete  
**Implementation Date:** May 8, 2026  
**Phase Duration:** ~2 hours

---

## Overview

Phase 6 implements automated subscription lifecycle management for the Store Builder, including:
- Expiry reminder system (7-day warning)
- Automatic store deactivation for expired subscriptions
- Email and database notifications
- Scheduled daily checks via Laravel scheduler
- Comprehensive logging for monitoring

This ensures stores are automatically deactivated when subscriptions expire, and store owners receive timely warnings to renew before service interruption.

---

## Features Implemented

### 1. Expiry Notifications

**StoreSubscriptionExpiringNotification**
- Sent 7 days before subscription expires
- Multi-channel (email + database)
- Includes renewal link and expiry details
- Warning emoji (⚠️) for urgency
- Lists consequences of expiration

**StoreSubscriptionExpiredNotification**
- Sent when subscription expires
- Confirms store deactivation
- Reassures data is preserved
- Includes renewal link
- Error emoji (⛔) for critical status

### 2. Scheduled Jobs

**CheckStoreSubscriptionExpiryJob**
- Runs daily at 8:00 AM
- Scans all active stores
- Identifies stores expiring in ≤7 days
- Identifies stores with expired subscriptions
- Dispatches individual reminder/deactivation jobs
- Comprehensive logging

**SendStoreExpiryReminderJob**
- Dispatched for each expiring store
- Checks if reminder already sent
- Calculates days remaining
- Sends expiry notification
- Marks `expiry_reminder_sent` as true
- Handles failures gracefully

**DeactivateExpiredStoreJob**
- Dispatched for each expired store
- Verifies subscription has expired
- Sets `is_active` to false
- Sets `status` to 'inactive'
- Sends expiration notification
- Logs deactivation details

### 3. Subscription Lifecycle

```
Day -7: CheckStoreSubscriptionExpiryJob runs at 8:00 AM
        ↓
        Finds store expiring in 7 days
        ↓
        Dispatches SendStoreExpiryReminderJob
        ↓
        Sends StoreSubscriptionExpiringNotification
        ↓
        Sets expiry_reminder_sent = true

Day 0:  CheckStoreSubscriptionExpiryJob runs at 8:00 AM
        ↓
        Finds store expired today
        ↓
        Dispatches DeactivateExpiredStoreJob
        ↓
        Sets is_active = false, status = 'inactive'
        ↓
        Sends StoreSubscriptionExpiredNotification
        ↓
        Store now unavailable to customers
```

### 4. Reminder Reset System

The `expiry_reminder_sent` flag is automatically reset when:
- Store subscription is renewed (webhook handlers)
- Store subscription is manually activated (StoreSubscriptionController)
- Subscription payment is processed (StoreSubscriptionService)

This ensures reminders are sent for each subscription period.

---

## Files Created

### Notifications
```
app/Notifications/StoreSubscriptionExpiringNotification.php  (110 lines)
app/Notifications/StoreSubscriptionExpiredNotification.php   (106 lines)
```

### Jobs
```
app/Jobs/SendStoreExpiryReminderJob.php          (102 lines)
app/Jobs/DeactivateExpiredStoreJob.php           (94 lines)
app/Jobs/CheckStoreSubscriptionExpiryJob.php     (103 lines)
```

---

## Files Modified

### routes/console.php
**Added:**
- Import for `CheckStoreSubscriptionExpiryJob`
- Scheduled job to run daily at 8:00 AM
- Description for clarity

**Schedule Entry:**
```php
Schedule::job(new CheckStoreSubscriptionExpiryJob())
    ->daily()
    ->at('08:00')
    ->description('Check store subscriptions, send expiry reminders, and deactivate expired stores');
```

---

## Database Schema

The `stores` table already includes the required field (added in Phase 1):

```sql
expiry_reminder_sent BOOLEAN DEFAULT FALSE
```

This field is automatically reset to `false` when:
- Subscription is renewed
- Payment webhook confirms successful renewal
- Manual subscription activation occurs

---

## Email Content

### Expiry Warning Email (7 days before)

**Subject:** ⚠️ Store Subscription Expiring Soon - [Store Name]

**Content:**
- Greeting with store owner name
- Store and plan details
- Expiry date with days remaining
- Consequences of expiration (bulleted list)
- Call-to-action: "Renew Subscription" button
- Footer with support information

### Expiration Email (after expiry)

**Subject:** ⛔ Store Subscription Expired - [Store Name]

**Content:**
- Greeting with store owner name
- Store and previous plan details
- Expiry date
- Confirmation of deactivation
- Reassurance about data preservation (bulleted list)
- Call-to-action: "Renew Subscription Now" button
- Footer with support information

---

## Logging

All jobs implement comprehensive logging for monitoring and debugging:

### CheckStoreSubscriptionExpiryJob Logs
```
[INFO] Starting store subscription expiry check
[INFO] Found stores expiring soon: count => 3
[INFO] Dispatched expiry reminder job: store_id => 12, expiry_date => 2026-05-15
[INFO] Found expired stores: count => 1
[INFO] Dispatched deactivation job: store_id => 8, expiry_date => 2026-05-07
[INFO] Store subscription expiry check completed
```

### SendStoreExpiryReminderJob Logs
```
[INFO] Expiry reminder sent successfully: store_id => 12, days_remaining => 7
[WARNING] Store not found for expiry reminder: store_id => 15
[INFO] Expiry reminder already sent for store: store_id => 12
[ERROR] Failed to send expiry reminder: store_id => 12, error => ...
```

### DeactivateExpiredStoreJob Logs
```
[INFO] Store deactivated due to expired subscription: store_id => 8, subscription_end_date => 2026-05-07
[INFO] Store already inactive: store_id => 9
[INFO] Store subscription is still active: store_id => 10
[ERROR] Failed to deactivate expired store: store_id => 8, error => ...
```

---

## Testing Guide

### 1. Test Expiry Reminder (7 Days)

**Setup:**
```bash
# Create a test store with subscription ending in 7 days
php artisan tinker

$user = User::find(1);
$store = $user->stores()->first();
$store->update([
    'subscription_end_date' => now()->addDays(7),
    'expiry_reminder_sent' => false,
    'is_active' => true,
]);
```

**Run Job Manually:**
```bash
php artisan tinker
dispatch(new \App\Jobs\CheckStoreSubscriptionExpiryJob());
```

**Verify:**
- Check `storage/logs/laravel.log` for job execution
- Check `jobs` table for `SendStoreExpiryReminderJob`
- Run queue worker: `php artisan queue:work`
- Check email inbox for expiry warning
- Verify `expiry_reminder_sent = true` in database
- Check `notifications` table for database notification

### 2. Test Store Deactivation (Expired)

**Setup:**
```bash
# Create a test store with expired subscription
php artisan tinker

$user = User::find(1);
$store = $user->stores()->first();
$store->update([
    'subscription_end_date' => now()->subDays(1),
    'is_active' => true,
    'status' => 'active',
]);
```

**Run Job Manually:**
```bash
php artisan tinker
dispatch(new \App\Jobs\CheckStoreSubscriptionExpiryJob());
```

**Verify:**
- Check logs for deactivation job dispatch
- Check `jobs` table for `DeactivateExpiredStoreJob`
- Run queue worker: `php artisan queue:work`
- Verify `is_active = false` in database
- Verify `status = 'inactive'` in database
- Check email inbox for expiration notification
- Try accessing storefront (should show unavailable page)

### 3. Test Scheduler Locally

**Option A: Run scheduler in foreground (testing only)**
```bash
php artisan schedule:work
```

Wait until 8:00 AM (or modify schedule time for testing), then verify:
- Logs show job dispatch
- Jobs are queued
- Queue worker processes jobs

**Option B: Simulate scheduler run**
```bash
# Run all scheduled tasks now (ignore time)
php artisan schedule:run
```

Verify:
- `CheckStoreSubscriptionExpiryJob` dispatched
- Logs show execution
- Jobs queued correctly

**Option C: Test specific time**
```bash
# Temporarily change schedule time in console.php to current time + 1 minute
# routes/console.php: ->at('14:35') instead of ->at('08:00')
php artisan schedule:work
```

### 4. Test Reminder Already Sent

**Setup:**
```bash
php artisan tinker

$store = Store::find(1);
$store->update([
    'subscription_end_date' => now()->addDays(7),
    'expiry_reminder_sent' => true,
]);
```

**Run Job:**
```bash
php artisan tinker
dispatch(new \App\Jobs\SendStoreExpiryReminderJob($store));
```

**Verify:**
- Job completes successfully
- No email sent
- Log shows "Expiry reminder already sent for store"

### 5. Test Reminder Reset on Renewal

**Setup:**
```bash
# Simulate subscription renewal webhook
php artisan tinker

$store = Store::find(1);
$store->update(['expiry_reminder_sent' => true]);

# Create and process subscription payment
// See STORE_BUILDER_PHASE5_COMPLETE.md for webhook testing
```

**Verify:**
- After webhook processes successfully
- `expiry_reminder_sent = false` in database
- Store can receive new reminders for next period

---

## Production Deployment

### 1. Environment Setup

Ensure cron job is configured on server:

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler (runs every minute)
* * * * * cd /home/boomcvmo/clicksintel.com && php artisan schedule:run >> /dev/null 2>&1
```

**Verify cron is working:**
```bash
# Check cron logs
grep CRON /var/log/syslog

# Or check Laravel logs
tail -f storage/logs/laravel.log
```

### 2. Queue Worker Setup

Ensure queue worker is running (required for processing jobs):

**Option A: Supervisor (recommended for production)**

Create supervisor config: `/etc/supervisor/conf.d/dealsintel-worker.conf`

```ini
[program:dealsintel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/boomcvmo/clicksintel.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=boomcvmo
numprocs=4
redirect_stderr=true
stdout_logfile=/home/boomcvmo/clicksintel.com/storage/logs/worker.log
stopwaitsecs=3600
```

**Start supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dealsintel-worker:*
```

**Option B: Systemd Service**

Create service file: `/etc/systemd/system/dealsintel-worker.service`

```ini
[Unit]
Description=DealsIntel Queue Worker
After=network.target

[Service]
Type=simple
User=boomcvmo
WorkingDirectory=/home/boomcvmo/clicksintel.com
ExecStart=/usr/bin/php /home/boomcvmo/clicksintel.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

**Enable and start:**
```bash
sudo systemctl enable dealsintel-worker
sudo systemctl start dealsintel-worker
sudo systemctl status dealsintel-worker
```

### 3. Monitoring

**Check scheduled jobs:**
```bash
php artisan schedule:list
```

**Monitor queue:**
```bash
php artisan queue:monitor
```

**Check logs:**
```bash
tail -f storage/logs/laravel.log | grep -i "subscription\|expiry\|deactivate"
```

**Using Laravel Telescope:**
- Navigate to `/telescope` in browser
- Check "Jobs" section for scheduled jobs
- Check "Mail" section for sent notifications
- Monitor for failed jobs

---

## Configuration

### Customize Schedule Time

Edit `routes/console.php` to change when the check runs:

```php
// Current: Daily at 8:00 AM
Schedule::job(new CheckStoreSubscriptionExpiryJob())
    ->daily()
    ->at('08:00');

// Change to 6:00 AM
Schedule::job(new CheckStoreSubscriptionExpiryJob())
    ->daily()
    ->at('06:00');

// Change to every 12 hours
Schedule::job(new CheckStoreSubscriptionExpiryJob())
    ->twiceDaily(8, 20); // 8:00 AM and 8:00 PM
```

### Customize Reminder Days

Edit `app/Jobs/CheckStoreSubscriptionExpiryJob.php`:

```php
// Current: 7 days warning
->whereDate('subscription_end_date', '<=', Carbon::now()->addDays(7))

// Change to 3 days warning
->whereDate('subscription_end_date', '<=', Carbon::now()->addDays(3))

// Change to 14 days warning
->whereDate('subscription_end_date', '<=', Carbon::now()->addDays(14))
```

### Customize Email Content

Edit notification files:
- `app/Notifications/StoreSubscriptionExpiringNotification.php`
- `app/Notifications/StoreSubscriptionExpiredNotification.php`

Modify the `toMail()` method to change:
- Subject line
- Email copy
- Button text
- Action URL

---

## Troubleshooting

### Jobs Not Running

**Check 1: Verify cron job**
```bash
crontab -l
# Should show: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Check 2: Verify schedule registered**
```bash
php artisan schedule:list
# Should show CheckStoreSubscriptionExpiryJob at 08:00
```

**Check 3: Check Laravel logs**
```bash
tail -f storage/logs/laravel.log
# Look for schedule:run entries
```

**Solution:** Ensure cron is configured and scheduler is running

### Emails Not Sending

**Check 1: Queue worker running?**
```bash
ps aux | grep "queue:work"
# Should show active queue worker process
```

**Check 2: Check failed jobs**
```bash
php artisan queue:failed
# Shows any jobs that failed
```

**Check 3: Test email configuration**
```bash
php artisan tinker
Mail::raw('Test email', function($msg) { 
    $msg->to('test@example.com')->subject('Test'); 
});
```

**Solution:** Start queue worker and verify mail configuration

### Store Not Deactivated

**Check 1: Verify subscription end date**
```bash
php artisan tinker
Store::find(1)->subscription_end_date; // Should be past date
```

**Check 2: Check if job dispatched**
```bash
# Check logs
grep "DeactivateExpiredStoreJob" storage/logs/laravel.log
```

**Check 3: Manually run job**
```bash
php artisan tinker
dispatch(new \App\Jobs\DeactivateExpiredStoreJob(Store::find(1)));
exit
php artisan queue:work --once
```

**Solution:** Verify subscription dates and check job logs

### Reminder Sent Multiple Times

**Check 1: Verify flag is being set**
```bash
php artisan tinker
Store::find(1)->expiry_reminder_sent; // Should be true after sending
```

**Check 2: Check if flag is being reset prematurely**
```bash
# Search for code that sets expiry_reminder_sent = false
grep -r "expiry_reminder_sent.*false" app/
```

**Solution:** Ensure `expiry_reminder_sent` is set to true after sending and only reset on renewal

### Jobs Stuck in Queue

**Check 1: Queue connection**
```bash
php artisan queue:monitor
```

**Check 2: Check jobs table**
```bash
php artisan tinker
DB::table('jobs')->count(); // Number of pending jobs
```

**Check 3: Restart queue worker**
```bash
php artisan queue:restart
```

**Solution:** Clear stuck jobs and restart worker

---

## Security Considerations

### 1. Email Rate Limiting
- Notifications are queued to prevent overwhelming mail server
- Failed jobs automatically retry (3 attempts)
- Exponential backoff for failed jobs

### 2. Data Integrity
- Jobs reload model data to ensure accuracy
- Transaction rollback on failures
- Comprehensive error logging

### 3. Performance
- Single daily check reduces database load
- Individual jobs dispatched asynchronously
- Queue processing prevents blocking

---

## Next Steps

### Phase 7: Analytics & Reporting (Upcoming)
- Store performance dashboard
- Sales analytics with charts
- Product performance tracking
- Revenue reports
- Order analytics
- Customer insights

### Optional Enhancements for Phase 6
- Grace period before deactivation (e.g., 3 days)
- Multiple reminder emails (7 days, 3 days, 1 day)
- SMS notifications via Termii
- Auto-renewal with saved payment methods
- Subscription upgrade/downgrade from dashboard

---

## Summary

Phase 6 successfully implements automated subscription lifecycle management:

✅ **Notifications Created:** 2 (Expiring, Expired)  
✅ **Jobs Created:** 3 (Check, Reminder, Deactivation)  
✅ **Scheduler Configured:** Daily at 8:00 AM  
✅ **Email Notifications:** Multi-channel (email + database)  
✅ **Logging:** Comprehensive for monitoring  
✅ **Testing:** Complete with manual test procedures  
✅ **Production Ready:** Cron and queue worker configuration documented

The subscription lifecycle is now fully automated, ensuring:
- Store owners receive timely warnings
- Expired stores are automatically deactivated
- No manual intervention required
- Comprehensive logging for monitoring
- Data integrity maintained

**Total Implementation Time:** ~2 hours  
**Lines of Code Added:** ~500 lines  
**Files Created:** 5  
**Files Modified:** 1

---

## Related Documentation

- [STORE_BUILDER_PHASE1_COMPLETE.md](STORE_BUILDER_PHASE1_COMPLETE.md) - Database models and migrations
- [STORE_BUILDER_PHASE4_COMPLETE.md](STORE_BUILDER_PHASE4_COMPLETE.md) - Public storefront
- [STORE_BUILDER_PHASE5_COMPLETE.md](STORE_BUILDER_PHASE5_COMPLETE.md) - Payment integration and webhooks
- [PAYMENT_GATEWAY_SETUP_GUIDE.md](PAYMENT_GATEWAY_SETUP_GUIDE.md) - Payment configuration guide

**Phase 6 Status:** ✅ COMPLETE
