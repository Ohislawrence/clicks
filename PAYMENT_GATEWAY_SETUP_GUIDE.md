# Store Builder - Payment Gateway Setup Guide

## Overview
The Store Builder supports two payment methods that advertisers can choose from:
1. **API Integration** - Advertisers enter their own Paystack/Flutterwave API keys
2. **Payment Links** - Advertisers create payment links from their Paystack/Flutterwave dashboard

---

## Payment Method 1: API Integration (Recommended)

### Benefits
- Seamless checkout experience within your store
- Automatic order confirmation
- Real-time stock updates
- Automated email notifications
- Full webhook integration

### Setup Process

#### For Paystack

**Step 1: Get Your API Keys**
1. Log in to your Paystack Dashboard (https://dashboard.paystack.com)
2. Navigate to **Settings** → **API Keys & Webhooks**
3. Copy your:
   - **Public Key** (pk_test_xxxxx or pk_live_xxxxx)
   - **Secret Key** (sk_test_xxxxx or sk_live_xxxxx)

**Step 2: Configure Webhooks**
1. In Paystack Dashboard, go to **Settings** → **API Keys & Webhooks**
2. Scroll to **Webhook URL** section
3. Add this URL: `https://yourdomain.com/api/webhooks/paystack/store-order`
4. Add this URL: `https://yourdomain.com/api/webhooks/paystack/store-subscription`
5. Save webhook configuration

**Step 3: Enter Keys in Store Settings**
1. In your Store Builder admin panel, go to **Store Settings** → **Payment Configuration**
2. Select **Payment Method**: API Integration
3. Select **Payment Provider**: Paystack
4. Enter your **Public Key**
5. Enter your **Secret Key**
6. Leave **Webhook Secret** empty (Paystack uses the same secret key for webhooks)
7. Save settings

**Note for Paystack:** The same secret key is used for both API calls and webhook verification, so you don't need to enter a separate webhook secret.

---

#### For Flutterwave

**Step 1: Get Your API Keys**
1. Log in to your Flutterwave Dashboard (https://dashboard.flutterwave.com)
2. Navigate to **Settings** → **API**
3. Copy your:
   - **Public Key** (FLWPUBK_TEST-xxxxx or FLWPUBK-xxxxx)
   - **Secret Key** (FLWSECK_TEST-xxxxx or FLWSECK-xxxxx)

**Step 2: Generate Webhook Secret Hash**
1. In Flutterwave Dashboard, go to **Settings** → **Webhooks**
2. Click **Generate Secret Hash** (or copy your existing hash)
3. Copy the **Secret Hash** (this is different from your Secret Key!)
4. **Important:** Save this hash - you'll need it for webhook verification

**Step 3: Configure Webhooks**
1. In Flutterwave Dashboard, go to **Settings** → **Webhooks**
2. Add webhook URL: `https://yourdomain.com/api/webhooks/flutterwave/store-order`
3. Add webhook URL: `https://yourdomain.com/api/webhooks/flutterwave/store-subscription`
4. Make sure your Secret Hash is displayed (generate one if you haven't)
5. Save webhook configuration

**Step 4: Enter Keys in Store Settings**
1. In your Store Builder admin panel, go to **Store Settings** → **Payment Configuration**
2. Select **Payment Method**: API Integration
3. Select **Payment Provider**: Flutterwave
4. Enter your **Public Key** (FLWPUBK_TEST-xxxxx)
5. Enter your **Secret Key** (FLWSECK_TEST-xxxxx)
6. Enter your **Webhook Secret Hash** (the hash you generated in Step 2)
7. Save settings

**Note for Flutterwave:** You MUST enter the webhook secret hash separately because Flutterwave uses a different value for webhook verification than the API secret key.

---

## Payment Method 2: Payment Links

### Benefits
- No API key management required
- Quick setup (paste a link)
- Good for testing or simple stores
- Payments go directly to your account

### Limitations
- No automatic order confirmation (manual verification required)
- No webhook integration
- No automatic stock updates
- No automated emails (you must notify customers manually)
- Customer must be manually redirected

### Setup Process

#### For Paystack Payment Links

**Step 1: Create Payment Link**
1. Log in to your Paystack Dashboard
2. Navigate to **Payments** → **Payment Pages**
3. Click **Create Payment Page**
4. Configure:
   - Page name (e.g., "Store Checkout")
   - Description
   - Amount (optional - can be customized per transaction)
5. Click **Create**
6. Copy the payment page URL (e.g., https://paystack.com/pay/xxxxx)

**Step 2: Enter Link in Store Settings**
1. In your Store Builder admin panel, go to **Store Settings** → **Payment Configuration**
2. Select **Payment Method**: Payment Link
3. Select **Payment Provider**: Paystack
4. Paste your **Payment Link**
5. Save settings

**Step 3: Manual Order Processing**
- When customers checkout, they'll be redirected to your Paystack payment page
- After payment, you must manually:
  - Check your Paystack dashboard for payments
  - Mark orders as "Paid" in the Store Builder
  - Update product stock manually
  - Send confirmation emails to customers

---

#### For Flutterwave Payment Links

**Step 1: Create Payment Link**
1. Log in to your Flutterwave Dashboard
2. Navigate to **Collections** → **Payment Links**
3. Click **Create Payment Link**
4. Configure:
   - Link name (e.g., "Store Checkout")
   - Description
   - Amount (optional - can be customized)
5. Click **Create**
6. Copy the payment link URL (e.g., https://flutterwave.com/pay/xxxxx)

**Step 2: Enter Link in Store Settings**
1. In your Store Builder admin panel, go to **Store Settings** → **Payment Configuration**
2. Select **Payment Method**: Payment Link
3. Select **Payment Provider**: Flutterwave
4. Paste your **Payment Link**
5. Save settings

**Step 3: Manual Order Processing**
- When customers checkout, they'll be redirected to your Flutterwave payment link
- After payment, you must manually:
  - Check your Flutterwave dashboard for payments
  - Mark orders as "Paid" in the Store Builder
  - Update product stock manually
  - Send confirmation emails to customers

---

## Webhook Configuration Details

### Understanding Webhooks

Webhooks allow payment gateways to notify your store when payments are completed. This enables:
- Automatic order confirmation
- Real-time stock updates
- Automated customer and store owner notifications
- Reliable payment verification (even if customer closes browser)

### Webhook URLs

Your platform provides these webhook endpoints:

**For Order Payments:**
- Paystack: `https://yourdomain.com/api/webhooks/paystack/store-order`
- Flutterwave: `https://yourdomain.com/api/webhooks/flutterwave/store-order`

**For Subscription Payments:**
- Paystack: `https://yourdomain.com/api/webhooks/paystack/store-subscription`
- Flutterwave: `https://yourdomain.com/api/webhooks/flutterwave/store-subscription`

### Security

All webhooks are verified using HMAC signatures:

**Paystack:**
- Uses your Secret Key for signature verification
- Header: `X-Paystack-Signature`
- Algorithm: HMAC SHA512

**Flutterwave:**
- Uses a separate Webhook Secret Hash for verification
- Header: `verif-hash`
- Algorithm: Hash comparison

**Important:** Each store's webhooks are verified using their own keys, ensuring secure multi-tenant operation.

---

## Database Fields

The `stores` table includes these payment-related fields:

```php
// Payment method selection
'payment_method' => 'api' or 'link'

// Payment provider
'payment_provider' => 'paystack' or 'flutterwave'

// For API Integration
'payment_public_key' => 'pk_test_xxxxx' (encrypted)
'payment_secret_key' => 'sk_test_xxxxx' (encrypted)
'payment_webhook_secret' => 'webhook_hash' (encrypted, optional for Paystack, required for Flutterwave)

// For Payment Links
'payment_link' => 'https://paystack.com/pay/xxxxx'
```

### Encryption

All payment credentials are encrypted using Laravel's `Crypt` facade:
- `payment_public_key` - Encrypted
- `payment_secret_key` - Encrypted
- `payment_webhook_secret` - Encrypted
- Never exposed in API responses (hidden in model)

---

## Testing Webhooks Locally

### Using ngrok

1. Install ngrok: https://ngrok.com/download
2. Start your local server (e.g., Laravel at port 80)
3. Run ngrok:
   ```bash
   ngrok http 80
   ```
4. Copy the ngrok URL (e.g., https://abc123.ngrok.io)
5. Update webhook URLs in Paystack/Flutterwave dashboard:
   - `https://abc123.ngrok.io/api/webhooks/paystack/store-order`
   - `https://abc123.ngrok.io/api/webhooks/flutterwave/store-order`

### Testing Process

1. Create a test store with API integration
2. Add test products
3. Make a test purchase using test cards:
   - **Paystack Test Card:** 5060 6666 6666 6666 666 (Mastercard)
   - **Flutterwave Test Card:** 4187 4274 1556 4246 (Visa)
4. Check logs to verify webhook received and processed
5. Verify order status updated to "Paid"
6. Verify stock deducted
7. Check email notifications sent

---

## Troubleshooting

### Webhooks Not Working

**Check 1: Verify webhook URL is correct**
- Must match exactly: `https://yourdomain.com/api/webhooks/paystack/store-order`
- No trailing slash
- HTTPS required in production

**Check 2: Verify webhook secret is correct**
- For Paystack: Should match secret key (or leave blank)
- For Flutterwave: Must be the webhook secret hash (not API secret key)

**Check 3: Check logs**
```bash
php artisan tail
# or
tail -f storage/logs/laravel.log
```

Look for:
- "Invalid webhook signature" - Keys don't match
- "Order not found" - Reference mismatch
- "Already processed" - Duplicate webhook (expected)

**Check 4: Test webhook manually**
Use tools like Postman or curl to send test webhooks:
```bash
curl -X POST https://yourdomain.com/api/webhooks/paystack/store-order \
  -H "Content-Type: application/json" \
  -H "X-Paystack-Signature: your_signature" \
  -d '{"event":"charge.success","data":{"reference":"TEST_123"}}'
```

### Orders Not Updating

**Possible causes:**
1. Webhook signature verification failing (check keys)
2. Order reference mismatch
3. Database transaction rolled back (check error logs)
4. Stock already deducted (webhook processed twice)

**Solution:** Check `storage/logs/laravel.log` for error details

### Emails Not Sending

**Check mail configuration:**
```bash
php artisan config:clear
php artisan queue:work
```

Emails are queued, so make sure queue worker is running.

---

## Recommendations

### For Production Stores
- ✅ Use **API Integration** (not payment links)
- ✅ Use **Live Keys** (not test keys)
- ✅ Configure **webhooks** properly
- ✅ Test with **test mode** first
- ✅ Monitor **webhook logs** regularly
- ✅ Keep **secret keys secure** (never commit to git)

### For Testing
- ✅ Use **test keys** from Paystack/Flutterwave
- ✅ Use **ngrok** for local webhook testing
- ✅ Monitor **Laravel logs** for debugging
- ✅ Test both **successful** and **failed** payments
- ✅ Verify **email notifications** sent

### Security Best Practices
- ✅ **Never share** your secret keys
- ✅ **Rotate keys** if compromised
- ✅ **Use HTTPS** in production
- ✅ **Monitor webhook logs** for suspicious activity
- ✅ **Validate all webhook** signatures
- ✅ **Encrypt keys** in database (done automatically)

---

## Summary

| Feature | API Integration | Payment Link |
|---------|----------------|--------------|
| Automatic order confirmation | ✅ Yes | ❌ No (manual) |
| Webhook integration | ✅ Yes | ❌ No |
| Automatic stock updates | ✅ Yes | ❌ No (manual) |
| Automated emails | ✅ Yes | ❌ No (manual) |
| Seamless checkout | ✅ Yes | ❌ Redirects away |
| Setup complexity | 🟡 Medium | 🟢 Easy |
| Recommended for | Production stores | Testing only |

**Recommendation:** Use API Integration with webhooks for the best customer experience and automated order management.
