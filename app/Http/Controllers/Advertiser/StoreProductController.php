<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\StoreCategory;
use App\Models\StoreProduct;
use App\Models\StoreProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreProductController extends Controller
{
    /**
     * Display a listing of store products
     */
    public function index($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $products = $store->products()
            ->withTrashed()
            ->with(['offer:id,store_product_id,name,approval_status,is_active'])
            ->latest()
            ->get();

        return Inertia::render('Advertiser/Store/Products/Index', [
            'products' => $products,
            'store' => $store->load('plan'),
            'productLimit' => $store->plan->product_limit,
            'canAddMore' => $store->plan->product_limit === null || $products->whereNull('deleted_at')->count() < $store->plan->product_limit,
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $store->load('plan');

        // Check product limit
        $currentCount = $store->products()->count();
        if ($store->plan->product_limit !== null && $currentCount >= $store->plan->product_limit) {
            return redirect()->route('advertiser.store.products.index', ['store' => $store->id])
                ->withErrors(['error' => 'You have reached your product limit. Upgrade your plan to add more products.']);
        }

        $states = State::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);
        $categories = $store->categories()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);

        return Inertia::render('Advertiser/Store/Products/Create', [
            'store' => $store,
            'states' => $states,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Check product limit
        $currentCount = $store->products()->count();
        if ($store->plan->product_limit !== null && $currentCount >= $store->plan->product_limit) {
            return back()->withErrors(['error' => 'You have reached your product limit. Upgrade your plan to add more products.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:tangible,digital',
            'delivery_fees' => 'nullable|array',
            'delivery_fees.*.state_id' => 'required_with:delivery_fees|integer|exists:states,id',
            'delivery_fees.*.fee' => 'required_with:delivery_fees|numeric|min:0',
            // Categories & Tags
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:store_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            // Variants
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.options' => 'required_with:variants|array',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.is_active' => 'boolean',
            // Digital delivery
            'download_url' => 'nullable|url|max:2048',
            'download_file' => 'nullable|file|max:102400', // 100 MB
            'download_expiry_hours' => 'nullable|integer|min:1',
            'max_downloads' => 'nullable|integer|min:1',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure unique slug within store
        $count = 1;
        $originalSlug = $validated['slug'];
        while ($store->products()->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Handle image uploads
        $imageUrls = [];
        if ($request->has('images') && is_array($request->file('images'))) {
            $uploadedFiles = $request->file('images');
            
            foreach ($uploadedFiles as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    
                    // 1. Create a unique filename
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // 2. Define the manual path to your public storage folder
                    // On Namecheap, public_path('storage/store-products') usually works
                    $destinationPath = public_path('storage/store-products');

                    // 3. Move the file manually
                    if ($file->move($destinationPath, $fileName)) {
                        // 4. Manually construct the URL
                        $imageUrls[] = asset('storage/store-products/' . $fileName);
                    }
                }
            }
        }

        $product = $store->products()->create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'images' => $imageUrls,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'product_type' => $validated['product_type'],
            'delivery_fees' => $validated['product_type'] === 'tangible' ? ($validated['delivery_fees'] ?? []) : null,
            'tags' => $validated['tags'] ?? null,
            'download_url' => $validated['product_type'] === 'digital' ? ($validated['download_url'] ?? null) : null,
            'download_expiry_hours' => $validated['product_type'] === 'digital' ? ($validated['download_expiry_hours'] ?? null) : null,
            'max_downloads' => $validated['product_type'] === 'digital' ? ($validated['max_downloads'] ?? null) : null,
        ]);

        // Handle digital file upload
        if ($validated['product_type'] === 'digital' && $request->hasFile('download_file')) {
            $file = $request->file('download_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/store-digital'), $fileName);
            $product->update(['download_file' => asset('storage/store-digital/' . $fileName)]);
        }

        // Sync categories
        if (!empty($validated['category_ids'])) {
            $product->categories()->sync($validated['category_ids']);
        }

        // Create variants
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $i => $variantData) {
                $product->variants()->create([
                    'name' => $variantData['name'],
                    'options' => $variantData['options'],
                    'price' => $variantData['price'] ?? null,
                    'compare_at_price' => $variantData['compare_at_price'] ?? null,
                    'stock_quantity' => $variantData['stock_quantity'] ?? null,
                    'sku' => $variantData['sku'] ?? null,
                    'is_active' => $variantData['is_active'] ?? true,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('advertiser.store.products.index', ['store' => $store->id])
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing a product
     */
    public function edit($storeId, StoreProduct $product)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure product belongs to user's store
        if ($product->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        $states = State::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);
        $categories = $store->categories()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);

        return Inertia::render('Advertiser/Store/Products/Edit', [
            'product' => $product->load('categories', 'variants'),
            'store' => $store,
            'states' => $states,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $storeId, StoreProduct $product)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure product belongs to user's store
        if ($product->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'existing_images' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'product_type' => 'required|in:tangible,digital',
            'delivery_fees' => 'nullable|array',
            'delivery_fees.*.state_id' => 'required_with:delivery_fees|integer|exists:states,id',
            'delivery_fees.*.fee' => 'required_with:delivery_fees|numeric|min:0',
            // Categories & Tags
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:store_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            // Variants
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.options' => 'required_with:variants|array',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.is_active' => 'boolean',
            // Digital delivery
            'download_url' => 'nullable|url|max:2048',
            'download_file' => 'nullable|file|max:102400',
            'remove_download_file' => 'boolean',
            'download_expiry_hours' => 'nullable|integer|min:1',
            'max_downloads' => 'nullable|integer|min:1',
        ]);

        // Ensure unique slug within store (excluding current product)
        $count = 1;
        $originalSlug = $validated['slug'];
        while ($store->products()->where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Handle image uploads
        $imageUrls = $validated['existing_images'] ?? [];

        if ($request->has('images') && is_array($request->file('images'))) {
            $uploadedFiles = $request->file('images');
            
            foreach ($uploadedFiles as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                    
                    // 1. Create a unique filename
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // 2. Define the manual path to your public storage folder
                    // On Namecheap, public_path('storage/store-products') usually works
                    $destinationPath = public_path('storage/store-products');

                    // 3. Move the file manually
                    if ($file->move($destinationPath, $fileName)) {
                        // 4. Manually construct the URL
                        $imageUrls[] = asset('storage/store-products/' . $fileName);
                    }
                }
            }
        }

        // Delete old images that are not in existing_images
        if ($product->images) {
            foreach ($product->images as $oldImage) {
                if (!in_array($oldImage, $imageUrls)) {
                    // Extract filename from URL and delete manually
                    $fileName = basename(parse_url($oldImage, PHP_URL_PATH));
                    $filePath = public_path('storage/store-products/' . $fileName);
                    
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        $product->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'images' => $imageUrls,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'product_type' => $validated['product_type'],
            'delivery_fees' => $validated['product_type'] === 'tangible' ? ($validated['delivery_fees'] ?? []) : null,
            'tags' => $validated['tags'] ?? null,
            'download_url' => $validated['product_type'] === 'digital' ? ($validated['download_url'] ?? null) : null,
            'download_expiry_hours' => $validated['product_type'] === 'digital' ? ($validated['download_expiry_hours'] ?? null) : null,
            'max_downloads' => $validated['product_type'] === 'digital' ? ($validated['max_downloads'] ?? null) : null,
        ]);

        // Handle digital file upload or removal
        if ($validated['product_type'] === 'digital') {
            if ($request->hasFile('download_file')) {
                // Delete old file if present
                if ($product->download_file) {
                    $oldPath = public_path('storage/store-digital/' . basename(parse_url($product->download_file, PHP_URL_PATH)));
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $file = $request->file('download_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/store-digital'), $fileName);
                $product->update(['download_file' => asset('storage/store-digital/' . $fileName)]);
            } elseif (!empty($validated['remove_download_file']) && $product->download_file) {
                $oldPath = public_path('storage/store-digital/' . basename(parse_url($product->download_file, PHP_URL_PATH)));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $product->update(['download_file' => null]);
            }
        } else {
            // Clear digital fields if switched to tangible
            $product->update(['download_file' => null, 'download_url' => null, 'download_expiry_hours' => null, 'max_downloads' => null]);
        }

        // Sync categories
        $product->categories()->sync($validated['category_ids'] ?? []);

        // Sync variants: delete removed, update existing, create new
        $incomingVariants = $validated['variants'] ?? [];
        $incomingIds = array_filter(array_column($incomingVariants, 'id'));
        $product->variants()->whereNotIn('id', $incomingIds)->delete();

        foreach ($incomingVariants as $i => $variantData) {
            $payload = [
                'name' => $variantData['name'],
                'options' => $variantData['options'],
                'price' => $variantData['price'] ?? null,
                'compare_at_price' => $variantData['compare_at_price'] ?? null,
                'stock_quantity' => $variantData['stock_quantity'] ?? null,
                'sku' => $variantData['sku'] ?? null,
                'is_active' => $variantData['is_active'] ?? true,
                'sort_order' => $i,
            ];
            if (!empty($variantData['id'])) {
                $product->variants()->where('id', $variantData['id'])->update($payload);
            } else {
                $product->variants()->create($payload);
            }
        }

        return redirect()->route('advertiser.store.products.index', ['store' => $store->id])
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy($storeId, StoreProduct $product)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure product belongs to user's store
        if ($product->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        // Soft delete
        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product active status
     */
    public function toggle($storeId, StoreProduct $product)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure product belongs to user's store
        if ($product->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return back()->with('success', 'Product status updated successfully!');
    }

    /**
     * Toggle product featured status
     */
    public function toggleFeatured($storeId, StoreProduct $product)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Ensure product belongs to user's store
        if ($product->store_id !== $store->id) {
            abort(403, 'Unauthorized');
        }

        $product->update([
            'is_featured' => !$product->is_featured,
        ]);

        return back()->with('success', 'Product featured status updated successfully!');
    }
}
