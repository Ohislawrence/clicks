<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreCategoryController extends Controller
{
    public function index($storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        $categories = $store->categories()->withCount('products')->get();

        return Inertia::render('Advertiser/Store/Categories/Index', [
            'store' => $store->load('plan'),
            'categories' => $categories,
        ]);
    }

    public function create($storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        return Inertia::render('Advertiser/Store/Categories/Form', [
            'store' => $store,
            'category' => null,
        ]);
    }

    public function store(Request $request, $storeId)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i = 1;
        while ($store->categories()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $store->categories()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('advertiser.store.categories.index', $store->id)
            ->with('success', 'Category created successfully.');
    }

    public function edit($storeId, StoreCategory $category)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($category->store_id !== $store->id) {
            abort(403);
        }

        return Inertia::render('Advertiser/Store/Categories/Form', [
            'store' => $store,
            'category' => $category,
        ]);
    }

    public function update(Request $request, $storeId, StoreCategory $category)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($category->store_id !== $store->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Re-generate slug only if name changed
        if ($category->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $base = $slug;
            $i = 1;
            while ($store->categories()->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $validated['slug'] = $slug;
        }

        $category->update($validated);

        return redirect()->route('advertiser.store.categories.index', $store->id)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy($storeId, StoreCategory $category)
    {
        $store = auth()->user()->stores()->findOrFail($storeId);

        if ($category->store_id !== $store->id) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('advertiser.store.categories.index', $store->id)
            ->with('success', 'Category deleted.');
    }
}
