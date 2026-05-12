# Store Builder Feature - Implementation Plan

## 📋 Feature Overview

Enable advertisers to create custom online stores (single product or multi-product) with subscription-based pricing and integrated payment processing.

**Store URL Format**: `/store/{business-slug}`

---

## 🎯 Core Requirements

### 1. Store Types
- **Single Product Store**: One product page with checkout
- **Multi-Product Store**: Full catalog with shopping cart

### 2. Template System
- **Theme-based**: Pre-configured base themes (layouts/structures)
- **Full Customization**: Advertisers can customize layouts, fonts, colors, spacing, etc.
- **Admin Management**: Admin creates base theme templates
- **No File Uploads**: All themes defined in database (JSON configuration)

### 3. Payment Options
- **Payment Link**: Manual checkout via Paystack/Flutterwave link
- **API Integration**: Direct checkout using advertiser's payment keys (encrypted storage)
- Customers pay directly to advertiser's account

### 4. Subscription System
- **Plans**: Single Product Plan & Multi-Product Plans (Bronze/Silver/Gold)
- **Billing**: Monthly or Yearly (with admin-configurable discount) - Manual renewal
- **Limits**: Multi-product plans limited by product count
- **Expiry Handling**: Store becomes unavailable after subscription expires
- **Notifications**: Reminder sent 7 days before expiry and after expiry

### 5. Store Management
- Logo upload
- Business name & description
- Product management (name, price, rich text description with Quill editor, images)
- Theme customization (layouts, fonts, colors, spacing)
- Order management (basic - view orders from payment webhooks)

---

## 📊 Database Schema

### 1. `store_plans` Table
Subscription plans configuration managed by admin.

```php
Schema::create('store_plans', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // 'Single Product', 'Multi-Product Bronze', etc.
    $table->string('slug')->unique(); // 'single-product', 'multi-bronze', etc.
    $table->enum('store_type', ['single', 'multi']); // Store type
    $table->integer('product_limit')->nullable(); // NULL for single, number for multi
    $table->decimal('monthly_price', 10, 2); // Monthly subscription price
    $table->decimal('yearly_price', 10, 2); // Yearly subscription price
    $table->integer('yearly_discount_percent')->default(0); // e.g., 20 = 20% off
    $table->json('features')->nullable(); // Additional features as JSON
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Sample Data**:
```php
// Single Product Plan
['name' => 'Single Product Store', 'store_type' => 'single', 'product_limit' => 1, 
 'monthly_price' => 5000, 'yearly_price' => 50000, 'yearly_discount_percent' => 17]

// Multi-Product Plans
['name' => 'Multi-Product Bronze', 'store_type' => 'multi', 'product_limit' => 10, 
 'monthly_price' => 15000, 'yearly_price' => 150000, 'yearly_discount_percent' => 17]
 
['name' => 'Multi-Product Silver', 'store_type' => 'multi', 'product_limit' => 50, 
 'monthly_price' => 30000, 'yearly_price' => 300000, 'yearly_discount_percent' => 17]
 
['name' => 'Multi-Product Gold', 'store_type' => 'multi', 'product_limit' => 200, 
 'monthly_price' => 50000, 'yearly_price' => 500000, 'yearly_discount_percent' => 17]
```

---

### 2. `store_themes` Table
Theme configurations created by admin.

```php
Schema::create('store_themes', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // 'Modern', 'Classic', 'Minimal', etc.
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('thumbnail')->nullable(); // Preview image
    $table->json('config'); // Theme configuration (see below)
    $table->enum('store_type', ['single', 'multi', 'both']); // Applicable store types
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Theme Config Structure** (JSON):
```json
{
  "layout": "classic|modern|minimal",
  "colors": {
    "primary": "#3B82F6",
    "secondary": "#10B981",
    "background": "#FFFFFF",
    "text": "#1F2937",
    "accent": "#F59E0B"
  },
  "typography": {
    "fontFamily": "Inter, sans-serif",
    "headingSize": "2xl"
  },
  "components": {
    "header": {"style": "centered|left|split"},
    "productCard": {"style": "card|list|grid"},
    "footer": {"style": "simple|detailed"}
  },
  "features": {
    "showBreadcrumbs": true,
    "showSearch": true,
    "showCategories": false
  }
}
```

---

### 3. `stores` Table
Advertiser's store configuration.

```php
Schema::create('stores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Advertiser
    $table->string('name'); // Business name
    $table->string('slug')->unique(); // URL slug
    $table->text('description')->nullable();
    $table->string('logo')->nullable(); // Logo image path
    $table->string('email')->nullable(); // Store contact email
    $table->string('phone')->nullable(); // Store contact phone
    $table->text('address')->nullable(); // Business address
    
    // Subscription
    $table->foreignId('store_plan_id')->constrained()->onDelete('restrict');
    $table->enum('billing_cycle', ['monthly', 'yearly']);
    $table->date('subscription_start_date');
    $table->date('subscription_end_date');
    $table->enum('subscription_status', ['active', 'expired', 'cancelled'])->default('active');
    $table->boolean('expiry_reminder_sent')->default(false); // Track if 7-day reminder sent
    
    // Theme - Advertiser has full control over customization
    $table->foreignId('store_theme_id')->constrained()->onDelete('restrict'); // Base template
    $table->json('theme_customization')->nullable(); // Full theme config: layout, colors, fonts, spacing, etc.
    
    // Payment Configuration
    $table->enum('payment_method', ['link', 'api']); // 'link' or 'api'
    $table->string('payment_provider')->nullable(); // 'paystack', 'flutterwave'
    $table->text('payment_link')->nullable(); // For 'link' method
    $table->text('payment_public_key')->nullable(); // Encrypted - for 'api' method
    $table->text('payment_secret_key')->nullable(); // Encrypted - for 'api' method
    
    // SEO
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('meta_image')->nullable();
    
    // Status
    $table->boolean('is_active')->default(true);
    $table->timestamp('published_at')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4. `store_products` Table
Products in multi-product stores.

```php
Schema::create('store_products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('store_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('slug'); // Unique per store
    $table->longText('description')->nullable(); // Rich text with Quill editor
    $table->decimal('price', 10, 2);
    $table->decimal('compare_at_price', 10, 2)->nullable(); // Original price for discounts
    $table->string('sku')->nullable();
    $table->integer('stock_quantity')->nullable(); // NULL = unlimited
    $table->json('images')->nullable(); // Array of image paths
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();
    
    $table->unique(['store_id', 'slug']);
});
```

---

### 5. `store_orders` Table
Orders placed on stores (basic tracking).

```php
Schema::create('store_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('store_id')->constrained()->onDelete('cascade');
    $table->string('order_number')->unique(); // Generated order ID
    
    // Customer Info
    $table->string('customer_name');
    $table->string('customer_email');
    $table->string('customer_phone')->nullable();
    $table->text('shipping_address')->nullable();
    
    // Order Items (JSON for simplicity)
    $table->json('items'); // [{product_id, name, price, quantity}]
    
    // Pricing
    $table->decimal('subtotal', 10, 2);
    $table->decimal('shipping_fee', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    
    // Payment
    $table->string('payment_reference')->nullable(); // Paystack/Flutterwave reference
    $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
    $table->timestamp('paid_at')->nullable();
    
    // Fulfillment
    $table->enum('fulfillment_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
    $table->text('notes')->nullable();
    
    $table->timestamps();
});
```

---

### 6. `store_subscriptions` Table
Track subscription payments history.

```php
Schema::create('store_subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('store_id')->constrained()->onDelete('cascade');
    $table->foreignId('store_plan_id')->constrained()->onDelete('restrict');
    $table->enum('billing_cycle', ['monthly', 'yearly']);
    $table->decimal('amount', 10, 2);
    $table->string('payment_reference')->unique(); // Paystack/Flutterwave reference
    $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
    $table->date('period_start');
    $table->date('period_end');
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});
```

---

## 🏗️ Implementation Phases

### **Phase 1: Database & Models** (Foundation)

**Tasks**:
1. Create all 6 migration files
2. Create Models with relationships:
   - `StorePlan` (admin configurable)
   - `StoreTheme` (admin configurable)
   - `Store` (belongs to User, Plan, Theme)
   - `StoreProduct` (belongs to Store)
   - `StoreOrder` (belongs to Store)
   - `StoreSubscription` (belongs to Store)

3. Add relationships to `User` model:
   ```php
   public function store() { return $this->hasOne(Store::class); }
   ```

4. Create seeders for:
   - Default store plans (4 plans)
   - Default store themes (3-5 themes)

**Duration**: 2-3 hours

---

### **Phase 2: Admin - Plans & Themes Management**

**Admin Routes** (`routes/web.php`):
```php
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // Store Plans
    Route::resource('store-plans', Admin\StorePlanController::class);
    
    // Store Themes
    Route::resource('store-themes', Admin\StoreThemeController::class);
    
    // Store Overview (view all stores)
    Route::get('stores', [Admin\StoreController::class, 'index'])->name('stores.index');
    Route::get('stores/{store}', [Admin\StoreController::class, 'show'])->name('stores.show');
});
```

**Controllers**:
- `Admin\StorePlanController` - CRUD for subscription plans
- `Admin\StoreThemeController` - CRUD for themes
- `Admin\StoreController` - View/manage all stores

**Pages** (Vue/Inertia):
- `Admin/StorePlans/Index.vue` - List plans with pricing
- `Admin/StorePlans/Create.vue` - Create/edit plan form
- `Admin/StoreThemes/Index.vue` - Theme gallery with previews
- `Admin/StoreThemes/Create.vue` - Theme configuration form
- `Admin/Stores/Index.vue` - All stores dashboard
- `Admin/Stores/Show.vue` - Store details & analytics

**Duration**: 4-5 hours

---

### **Phase 3: Advertiser - Store Creation & Management**

**Advertiser Routes** (`routes/web.php`):
```php
Route::prefix('advertiser')->middleware(['auth', 'role:advertiser'])->name('advertiser.')->group(function () {
    // Store Setup
    Route::get('store/plans', [Advertiser\StoreController::class, 'plans'])->name('store.plans');
    Route::post('store/subscribe', [Advertiser\StoreController::class, 'subscribe'])->name('store.subscribe');
    Route::get('store/create', [Advertiser\StoreController::class, 'create'])->name('store.create');
    Route::post('store', [Advertiser\StoreController::class, 'store'])->name('store.store');
    
    // Store Management
    Route::get('store', [Advertiser\StoreController::class, 'index'])->name('store.index');
    Route::get('store/edit', [Advertiser\StoreController::class, 'edit'])->name('store.edit');
    Route::put('store', [Advertiser\StoreController::class, 'update'])->name('store.update');
    Route::delete('store', [Advertiser\StoreController::class, 'destroy'])->name('store.destroy');
    
    // Products (multi-store only)
    Route::resource('store/products', Advertiser\StoreProductController::class);
    
    // Orders
    Route::get('store/orders', [Advertiser\StoreOrderController::class, 'index'])->name('store.orders.index');
    Route::get('store/orders/{order}', [Advertiser\StoreOrderController::class, 'show'])->name('store.orders.show');
    Route::put('store/orders/{order}/fulfill', [Advertiser\StoreOrderController::class, 'fulfill'])->name('store.orders.fulfill');
    
    // Subscription Management
    Route::get('store/subscription', [Advertiser\StoreSubscriptionController::class, 'index'])->name('store.subscription');
    Route::post('store/subscription/renew', [Advertiser\StoreSubscriptionController::class, 'renew'])->name('store.subscription.renew');
    Route::post('store/subscription/upgrade', [Advertiser\StoreSubscriptionController::class, 'upgrade'])->name('store.subscription.upgrade');
    Route::delete('store/subscription/cancel', [Advertiser\StoreSubscriptionController::class, 'cancel'])->name('store.subscription.cancel');
});
```

**Controllers**:
- `Advertiser\StoreController` - Store setup & configuration
- `Advertiser\StoreProductController` - Product CRUD
- `Advertiser\StoreOrderController` - Order management
- `Advertiser\StoreSubscriptionController` - Subscription management

**Pages** (Vue/Inertia):
- `Advertiser/Store/Plans.vue` - Choose plan (pricing table)
- `Advertiser/Store/Create.vue` - Store setup wizard (4 steps: info, theme customization, payment config, preview)
- `Advertiser/Store/Index.vue` - Store dashboard (stats, recent orders, expiry status)
- `Advertiser/Store/Edit.vue` - Edit store settings & theme customization
- `Advertiser/Store/Products/Index.vue` - Product list
- `Advertiser/Store/Products/Create.vue` - Add/edit product (with Quill editor for description)
- `Advertiser/Store/Orders/Index.vue` - Order list with filters
- `Advertiser/Store/Orders/Show.vue` - Order details
- `Advertiser/Store/Subscription.vue` - Subscription details, expiry date, renewal payment

**Duration**: 8-10 hours

---

### **Phase 4: Public Store Frontend**

**Public Routes** (`routes/web.php`):
```php
Route::prefix('store')->name('store.')->group(function () {
    Route::get('{slug}', [StoreController::class, 'show'])->name('show');
    Route::get('{slug}/product/{productSlug}', [StoreController::class, 'product'])->name('product');
    Route::post('{slug}/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::get('{slug}/order/{orderNumber}/thank-you', [StoreController::class, 'thankYou'])->name('thank-you');
});
```

**Controller**:
- `StoreController` - Public store pages

**Pages** (Vue/Inertia):
- `Store/Show.vue` - Store homepage (single or multi layout based on theme)
- `Store/Product.vue` - Product detail page
- `Store/Checkout.vue` - Checkout form (if using API integration)
- `Store/ThankYou.vue` - Order confirmation

**Theme Components** (reusable):
- `StoreLayout.vue` - Dynamic layout wrapper
- `StoreHeader.vue` - Header with logo & nav
- `StoreFooter.vue` - Footer
- `ProductCard.vue` - Product display card
- `ShoppingCart.vue` - Cart sidebar (multi-store)

**Duration**: 6-8 hours

---

### **Phase 5: Payment Integration**

**Services**:
- `app/Services/StorePaymentService.php` - Handle payment processing
- `app/Services/StoreSubscriptionService.php` - Handle subscription billing

**Webhooks** (`routes/api.php`):
```php
Route::post('webhooks/paystack/store', [WebhookController::class, 'paystackStore']);
Route::post('webhooks/flutterwave/store', [WebhookController::class, 'flutterwaveStore']);
```

**Features**:
1. **API Key Encryption**:
   ```php
   use Illuminate\Support\Facades\Crypt;
   
   $store->payment_secret_key = Crypt::encryptString($secretKey);
   $decrypted = Crypt::decryptString($store->payment_secret_key);
   ```

2. **Subscription Payment**:
   - One-time payment (no recurring)
   - Record payment in store_subscriptions
   - Calculate subscription period
   - Activate store

3. **Order Webhooks**:
   - Verify payment
   - Create order record
   - Send email notifications
   - Trigger fulfillment

**Duration**: 5-6 hours

---

### **Phase 6: Jobs & Notifications**

**Jobs**:
```php
app/Jobs/
├── ProcessStoreOrderJob.php         // Process order after payment
├── SendStoreOrderConfirmationJob.php // Email to customer & advertiser
├── CheckStoreSubscriptionExpiryJob.php // Daily check for expiring subscriptions
├── SendStoreExpiryReminderJob.php   // Send 7-day reminder
└── DeactivateExpiredStoreJob.php    // Deactivate expired stores
```

**Notifications**:
```php
app/Notifications/
├── StoreOrderReceivedNotification.php        // To advertiser (new order)
├── StoreOrderConfirmationNotification.php    // To customer (order confirmation)
├── StoreSubscriptionExpiringNotification.php // 7 days before expiry
├── StoreSubscriptionExpiredNotification.php  // After expiry (with renewal link)
└── StoreSubscriptionRenewedNotification.php  // After successful renewal
```

**Scheduler** (`routes/console.php`):
```php
// Send reminder 7 days before expiry
Schedule::command('stores:send-expiry-reminders')
    ->daily()
    ->at('08:00')
    ->description('Send 7-day expiry reminder to advertisers');

// Deactivate expired stores and notify
Schedule::command('stores:deactivate-expired')
    ->daily()
    ->at('00:30')
    ->description('Deactivate stores after subscription expires');
```

**Duration**: 3-4 hours

---

### **Phase 7: Analytics & Reporting**

**Features**:
- Store dashboard (sales, orders, visitors)
- Product performance
- Revenue charts (ApexCharts)
- Top products
- Recent orders

**Duration**: 3-4 hours

---

## 📦 Total Implementation Breakdown

| Phase | Component | Estimated Time |
|-------|-----------|----------------|
| 1 | Database & Models | 2-3 hours |
| 2 | Admin Management | 4-5 hours |
| 3 | Advertiser Interface | 8-10 hours |
| 4 | Public Store Frontend | 6-8 hours |
| 5 | Payment Integration | 5-6 hours |
| 6 | Jobs & Notifications | 3-4 hours |
| 7 | Analytics & Reporting | 3-4 hours |
| **Total** | | **31-40 hours** |

---

## 🔐 Security Considerations

1. **Payment Keys Encryption**:
   - Use Laravel's `Crypt` facade
   - Never expose secret keys in responses
   - Validate keys before storing

2. **Store Access Control**:
   - Only store owner can edit
   - Admin can view all stores
   - Public pages respect `is_active` status

3. **Webhook Verification**:
   - Verify Paystack/Flutterwave signatures
   - Log all webhook events
   - Idempotency for duplicate webhooks

4. **Rate Limiting**:
   - Checkout endpoints
   - Store creation (prevent abuse)

---

## 🎨 Theme Customization System

### For Admin (Base Templates)
Admin creates base theme templates with default configurations:
- Layout structure (modern, classic, minimal)
- Component placements
- Default color schemes
- Font options
- Preview thumbnails

### For Advertisers (Full Customization)
When creating/editing store, advertisers can customize:

**Colors** (Color pickers):
- Primary color
- Secondary color
- Background color
- Text color
- Accent color
- Button colors
- Link colors

**Typography**:
- Font family (from dropdown: Inter, Roboto, Poppins, Montserrat, etc.)
- Heading sizes (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
- Body text size
- Font weights

**Layout Options**:
- Header style (centered, left-aligned, split)
- Header transparency
- Product card style (card, grid, list)
- Card shadows (none, sm, md, lg, xl)
- Footer style (simple, detailed)
- Spacing (compact, normal, relaxed)

**Components**:
- Show/hide breadcrumbs
- Show/hide search bar
- Show/hide categories (multi-store)
- Enable/disable animations

**Live Preview**: Real-time preview of changes before saving

---

## 🎨 Sample Theme Configurations

### Theme 1: Modern Minimalist
```json
{
  "name": "Modern Minimalist",
  "layout": "modern",
  "colors": {
    "primary": "#000000",
    "secondary": "#FFFFFF",
    "background": "#F9FAFB",
    "text": "#111827",
    "accent": "#3B82F6"
  },
  "typography": {
    "fontFamily": "'Inter', sans-serif",
    "headingSize": "3xl"
  },
  "components": {
    "header": {"style": "centered", "transparent": true},
    "productCard": {"style": "card", "shadow": "md"},
    "footer": {"style": "simple"}
  }
}
```

### Theme 2: Classic E-commerce
```json
{
  "name": "Classic E-commerce",
  "layout": "classic",
  "colors": {
    "primary": "#10B981",
    "secondary": "#059669",
    "background": "#FFFFFF",
    "text": "#1F2937",
    "accent": "#F59E0B"
  },
  "typography": {
    "fontFamily": "'Roboto', sans-serif",
    "headingSize": "2xl"
  },
  "components": {
    "header": {"style": "split", "transparent": false},
    "productCard": {"style": "grid", "shadow": "lg"},
    "footer": {"style": "detailed"}
  }
}
```

### Theme 3: Bold & Vibrant
```json
{
  "name": "Bold & Vibrant",
  "layout": "modern",
  "colors": {
    "primary": "#EF4444",
    "secondary": "#F59E0B",
    "background": "#111827",
    "text": "#F9FAFB",
    "accent": "#8B5CF6"
  },
  "typography": {
    "fontFamily": "'Poppins', sans-serif",
    "headingSize": "4xl"
  },
  "components": {
    "header": {"style": "left", "transparent": false},
    "productCard": {"style": "list", "shadow": "xl"},
    "footer": {"style": "simple"}
  }
}
```

---

## 💡 Future Enhancements (Post-MVP)

1. **Advanced Features**:
   - **Product categories & tags**: Organize products into categories for easier browsing in multi-product stores
   - **Product variants (size, color)**: Allow a single product to have multiple variants (e.g. size S/M/L, color Red/Blue) each with its own price/stock
   - **Digital product delivery**: File download links or access keys auto-delivered to customer email after payment
   - **Discount codes**: Coupon/promo code system with fixed or percentage discounts, usage limits, and expiry dates
   - **Shipping zones & rates (Nigeria)**: ✅ *Implemented* — State-based delivery fee system using `countries` and `states` tables. Nigeria is seeded with all 36 states + FCT. Advertisers select states from a dropdown when setting delivery fees per product. Structure: `delivery_fees: [{state_id, fee}]` stored as JSON on the product.

2. **Marketing Tools**:
   - SEO optimization
   - Social media integration
   - Email marketing
   - Abandoned cart recovery

3. **Analytics**:
   - Google Analytics integration
   - Conversion tracking
   - Visitor heatmaps

4. **Custom Domains**:
   - Allow advertisers to use their own domains
   - SSL certificate automation

---

## 🚀 Next Steps

1. **Review & Approve** this implementation plan
2. **Phase 1**: Create database migrations and models
3. **Phase 2**: Build admin interface for plans & themes
4. **Phase 3**: Build advertiser store creation flow
5. **Phase 4**: Build public store pages
6. **Phase 5**: Integrate payment processing
7. **Phase 6**: Add background jobs & notifications
8. **Phase 7**: Add analytics dashboard

**Estimated Timeline**: 5-7 working days for full implementation

---

## 📝 Notes

- All prices are in NGN (Nigerian Naira) based on existing app currency
- **Manual Subscription Renewal**: No automatic recurring billing - advertisers must manually renew
- **Expiry Notifications**: 
  - 7-day reminder sent before subscription expires
  - Notification sent after expiry with renewal payment link
- **Store Deactivation**: Stores become unavailable immediately after subscription expires
- Admin can override subscription status and extend expiry dates manually
- **Rich Text Editor**: Product descriptions use Quill editor (same as blog posts)
- **Theme Customization**: Advertisers have full control over layouts, colors, fonts, spacing
- Product images stored in `storage/app/public/stores/{store-id}/products/`
- Store logos stored in `storage/app/public/stores/{store-id}/logo/`
- Theme preview images stored in `storage/app/public/store-themes/`
- **Payment Security**: Advertiser payment API keys encrypted using Laravel's `Crypt` facade

---

**Ready to proceed?** Let me know if you'd like to start implementation or need any adjustments to the plan!
