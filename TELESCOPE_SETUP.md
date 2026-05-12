# Laravel Telescope Setup Guide

## Overview

Laravel Telescope has been installed and configured for monitoring your application's performance, debugging, and troubleshooting.

## Access Configuration

### Authorization

✅ **Admin-Only Access**: Telescope is restricted to users with the **admin** role only.

- **Local Environment**: Anyone can access Telescope
- **Production**: Only users with `admin` role can access

### Access URL

Visit: `https://your-domain.com/admin/telescope`

For local development: `http://localhost/admin/telescope`

---

## Automatic Data Pruning

### Configuration

Telescope data is automatically cleaned up to prevent database bloat:

- **Retention Period**: 7 days (168 hours)
- **Pruning Schedule**: Daily at 2:00 AM
- **Command**: `telescope:prune --hours=168`

### Manual Pruning

To manually prune old data:

```bash
php artisan telescope:prune --hours=168
```

To prune data older than 24 hours:

```bash
php artisan telescope:prune --hours=24
```

---

## What Telescope Monitors

### ✅ Enabled Watchers

1. **Requests** - HTTP requests and responses
2. **Commands** - Artisan commands executed
3. **Schedule** - Scheduled tasks execution
4. **Jobs** - Queue job processing
5. **Exceptions** - Application errors
6. **Logs** - Error-level log entries
7. **Dumps** - `dd()` and `dump()` output
8. **Queries** - Database queries (slow queries highlighted)
9. **Models** - Eloquent model events
10. **Events** - Application events fired
11. **Mail** - Emails sent
12. **Notifications** - Notifications dispatched
13. **Cache** - Cache operations
14. **Redis** - Redis commands
15. **Gates** - Authorization checks

---

## Production Recommendations

### Enable Only in Production When Needed

For better performance, you can disable Telescope in production by default:

**`.env`**:
```env
TELESCOPE_ENABLED=false
```

Enable when debugging issues:
```env
TELESCOPE_ENABLED=true
```

### Filter Important Entries

The service provider is already configured to filter entries in production:

- ✅ Reportable exceptions
- ✅ Failed requests
- ✅ Failed jobs
- ✅ Scheduled tasks
- ✅ Tagged entries

### Database Optimization

Telescope creates several database tables. Ensure proper indexing:

```sql
-- Check telescope_entries table size
SELECT 
    TABLE_NAME AS 'Table',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'your_database_name'
AND TABLE_NAME LIKE 'telescope_%';
```

---

## Usage Tips

### 1. Debugging Slow Queries

Navigate to **Queries** tab in Telescope:
- Queries taking > 100ms are highlighted as "slow"
- Review and optimize these queries
- Add database indexes where needed

### 2. Tracking Failed Jobs

Navigate to **Jobs** tab:
- See which jobs failed and why
- Check retry attempts
- Review job payload and exception

### 3. Monitoring API Requests

Navigate to **Requests** tab:
- Filter by status code (4xx, 5xx errors)
- Check response times
- Review request payloads

### 4. Exception Tracking

Navigate to **Exceptions** tab:
- See all application exceptions
- Stack traces included
- Group by exception type

### 5. Debugging with Dumps

Use `dump()` or `dd()` in your code:
```php
dump($variable); // Output appears in Telescope's Dumps tab
```

---

## Troubleshooting

### Telescope Not Accessible

1. **Check if you're logged in** as an admin:
   ```bash
   php artisan tinker
   >>> $user = User::find(1);
   >>> $user->hasRole('admin'); // Should return true
   ```

2. **Clear config cache**:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

3. **Verify Telescope is installed**:
   ```bash
   php artisan telescope:install
   php artisan migrate
   ```

### Telescope Database Growing Too Large

1. **Run pruning manually**:
   ```bash
   php artisan telescope:prune --hours=24
   ```

2. **Reduce retention period** in `config/telescope.php`:
   ```php
   'prune' => [
       'hours' => 72, // Keep only 3 days
   ],
   ```

3. **Disable unnecessary watchers** in `config/telescope.php`:
   ```php
   Watchers\QueryWatcher::class => [
       'enabled' => false, // Disable query logging
   ],
   ```

### Performance Impact

If Telescope is slowing down your application:

1. **Disable in production** unless debugging:
   ```env
   TELESCOPE_ENABLED=false
   ```

2. **Use selective recording** in `TelescopeServiceProvider`:
   ```php
   Telescope::filter(function (IncomingEntry $entry) {
       return $entry->isReportableException() ||
              $entry->isFailedRequest() ||
              $entry->isFailedJob();
   });
   ```

---

## Configuration Files

### Service Provider
- **File**: `app/Providers/TelescopeServiceProvider.php`
- **Purpose**: Authorization gate, filtering, sensitive data hiding

### Configuration
- **File**: `config/telescope.php`
- **Purpose**: Watchers, storage, middleware, pruning settings

### Schedule
- **File**: `routes/console.php`
- **Pruning Command**: `telescope:prune --hours=168` runs daily at 2 AM

---

## Security Notes

✅ **Protected**: Only admin users can access Telescope in production

⚠️ **Warning**: Telescope logs sensitive data like:
- Request parameters
- Headers
- Database queries
- Job payloads

The configuration hides:
- `_token` parameters
- `cookie` headers
- `x-csrf-token` headers
- `x-xsrf-token` headers

**Never expose Telescope publicly without authentication!**

---

## Monitoring Checklist

- [x] Telescope installed
- [x] Migrations run
- [x] Admin-only authorization configured
- [x] Automatic pruning enabled (7 days)
- [x] Pruning scheduled daily
- [x] Sensitive data hidden
- [x] Production filtering enabled

**Your Telescope setup is complete and secure!** 🔭✨
