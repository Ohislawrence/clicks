<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreThemeController extends Controller
{
    /**
     * Display a listing of store themes.
     */
    public function index()
    {
        $themes = StoreTheme::withCount('stores')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/StoreThemes/Index', [
            'themes' => $themes,
        ]);
    }

    /**
     * Show the form for creating a new theme.
     */
    public function create()
    {
        return Inertia::render('Admin/StoreThemes/Create');
    }

    /**
     * Store a newly created theme.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:store_themes',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'store_type' => 'required|in:single,multi,both',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            // Layout
            'config.layout.header_style' => 'nullable|string',
            'config.layout.product_grid' => 'nullable|string',
            'config.layout.sidebar_position' => 'nullable|string',
            // Colors
            'config.colors.primary' => 'nullable|string',
            'config.colors.secondary' => 'nullable|string',
            'config.colors.accent' => 'nullable|string',
            'config.colors.text' => 'nullable|string',
            'config.colors.background' => 'nullable|string',
            // Typography
            'config.typography.heading_font' => 'nullable|string',
            'config.typography.body_font' => 'nullable|string',
            'config.typography.heading_size' => 'nullable|string',
            // Components
            'config.components.show_breadcrumbs' => 'nullable|boolean',
            'config.components.show_social_share' => 'nullable|boolean',
            'config.components.show_related_products' => 'nullable|boolean',
            'config.components.show_reviews' => 'nullable|boolean',
            // Features
            'config.features.sticky_header' => 'nullable|boolean',
            'config.features.quick_view' => 'nullable|boolean',
            'config.features.product_zoom' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('store-themes', 'public');
            $validated['thumbnail'] = $path;
        }

        // Build config array
        $config = [
            'layout' => $validated['config']['layout'] ?? [],
            'colors' => $validated['config']['colors'] ?? [],
            'typography' => $validated['config']['typography'] ?? [],
            'components' => $validated['config']['components'] ?? [],
            'features' => $validated['config']['features'] ?? [],
        ];

        // Remove config from validated to avoid duplication
        unset($validated['config']);
        $validated['config'] = $config;

        StoreTheme::create($validated);

        return redirect()->route('admin.store-themes.index')
            ->with('success', 'Store theme created successfully.');
    }

    /**
     * Show the form for editing the specified theme.
     */
    public function edit(StoreTheme $storeTheme)
    {
        return Inertia::render('Admin/StoreThemes/Edit', [
            'theme' => $storeTheme,
        ]);
    }

    /**
     * Update the specified theme.
     */
    public function update(Request $request, StoreTheme $storeTheme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:store_themes,slug,' . $storeTheme->id,
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'store_type' => 'required|in:single,multi,both',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            // Layout
            'config.layout.header_style' => 'nullable|string',
            'config.layout.product_grid' => 'nullable|string',
            'config.layout.sidebar_position' => 'nullable|string',
            // Colors
            'config.colors.primary' => 'nullable|string',
            'config.colors.secondary' => 'nullable|string',
            'config.colors.accent' => 'nullable|string',
            'config.colors.text' => 'nullable|string',
            'config.colors.background' => 'nullable|string',
            // Typography
            'config.typography.heading_font' => 'nullable|string',
            'config.typography.body_font' => 'nullable|string',
            'config.typography.heading_size' => 'nullable|string',
            // Components
            'config.components.show_breadcrumbs' => 'nullable|boolean',
            'config.components.show_social_share' => 'nullable|boolean',
            'config.components.show_related_products' => 'nullable|boolean',
            'config.components.show_reviews' => 'nullable|boolean',
            // Features
            'config.features.sticky_header' => 'nullable|boolean',
            'config.features.quick_view' => 'nullable|boolean',
            'config.features.product_zoom' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($storeTheme->thumbnail) {
                Storage::disk('public')->delete($storeTheme->thumbnail);
            }

            $path = $request->file('thumbnail')->store('store-themes', 'public');
            $validated['thumbnail'] = $path;
        }

        // Build config array
        if (isset($validated['config'])) {
            $config = [
                'layout' => $validated['config']['layout'] ?? [],
                'colors' => $validated['config']['colors'] ?? [],
                'typography' => $validated['config']['typography'] ?? [],
                'components' => $validated['config']['components'] ?? [],
                'features' => $validated['config']['features'] ?? [],
            ];

            // Remove config from validated to avoid duplication
            unset($validated['config']);
            $validated['config'] = $config;
        }

        $storeTheme->update($validated);

        return redirect()->route('admin.store-themes.index')
            ->with('success', 'Store theme updated successfully.');
    }

    /**
     * Remove the specified theme.
     */
    public function destroy(StoreTheme $storeTheme)
    {
        // Check if theme is in use
        if ($storeTheme->stores()->count() > 0) {
            return back()->with('error', 'Cannot delete theme that is currently in use by stores.');
        }

        // Delete thumbnail
        if ($storeTheme->thumbnail) {
            Storage::disk('public')->delete($storeTheme->thumbnail);
        }

        $storeTheme->delete();

        return redirect()->route('admin.store-themes.index')
            ->with('success', 'Store theme deleted successfully.');
    }

    /**
     * Toggle the active status of a theme.
     */
    public function toggle(StoreTheme $storeTheme)
    {
        $storeTheme->update([
            'is_active' => !$storeTheme->is_active,
        ]);

        $status = $storeTheme->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Store theme {$status} successfully.");
    }
}
