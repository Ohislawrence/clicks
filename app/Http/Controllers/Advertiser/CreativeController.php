<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCreative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CreativeController extends Controller
{
    public function index(Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $creatives = $offer->creatives()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($creative) {
                return [
                    'id' => $creative->id,
                    'type' => $creative->type,
                    'name' => $creative->name,
                    'file_path' => $creative->file_path,
                    'file_url' => $creative->file_url,
                    'content' => $creative->content,
                    'width' => $creative->width,
                    'height' => $creative->height,
                    'dimensions' => $creative->dimensions,
                    'size' => $creative->size,
                    'format' => $creative->format,
                    'clicks_count' => $creative->clicks_count,
                    'is_active' => $creative->is_active,
                    'created_at' => $creative->created_at,
                ];
            });

        return Inertia::render('Advertiser/Creatives/Index', [
            'offer' => [
                'id' => $offer->id,
                'name' => $offer->name,
            ],
            'creatives' => $creatives,
        ]);
    }

    public function store(Request $request, Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:banner,image,text,video',
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240', // 10MB max
            'content' => 'nullable|string',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
        ]);

        $creativesData = [
            'offer_id' => $offer->id,
            'type' => $validated['type'],
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'width' => $validated['width'] ?? null,
            'height' => $validated['height'] ?? null,
            'is_active' => true,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('creatives', 'public');
            
            $creativesData['file_path'] = $path;
            $creativesData['size'] = $this->formatFileSize($file->getSize());
            $creativesData['format'] = strtoupper($file->getClientOriginalExtension());

            // Get image dimensions if it's an image
            if (in_array($validated['type'], ['banner', 'image'])) {
                $imagePath = storage_path('app/public/' . $path);
                if (file_exists($imagePath)) {
                    $imageInfo = getimagesize($imagePath);
                    if ($imageInfo) {
                        $creativesData['width'] = $imageInfo[0];
                        $creativesData['height'] = $imageInfo[1];
                    }
                }
            }
        }

        OfferCreative::create($creativesData);

        return redirect()->back()->with('success', 'Creative added successfully!');
    }

    public function update(Request $request, Offer $offer, OfferCreative $creative)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id() || $creative->offer_id !== $offer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'is_active' => $validated['is_active'] ?? $creative->is_active,
        ];

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($creative->file_path) {
                Storage::disk('public')->delete($creative->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('creatives', 'public');
            
            $updateData['file_path'] = $path;
            $updateData['size'] = $this->formatFileSize($file->getSize());
            $updateData['format'] = strtoupper($file->getClientOriginalExtension());

            // Get image dimensions if it's an image
            if (in_array($creative->type, ['banner', 'image'])) {
                $imagePath = storage_path('app/public/' . $path);
                if (file_exists($imagePath)) {
                    $imageInfo = getimagesize($imagePath);
                    if ($imageInfo) {
                        $updateData['width'] = $imageInfo[0];
                        $updateData['height'] = $imageInfo[1];
                    }
                }
            }
        }

        $creative->update($updateData);

        return redirect()->back()->with('success', 'Creative updated successfully!');
    }

    public function destroy(Offer $offer, OfferCreative $creative)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id() || $creative->offer_id !== $offer->id) {
            abort(403);
        }

        // Delete file from storage if exists
        if ($creative->file_path) {
            Storage::disk('public')->delete($creative->file_path);
        }

        $creative->delete();

        return redirect()->back()->with('success', 'Creative deleted successfully!');
    }

    public function toggleStatus(Offer $offer, OfferCreative $creative)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id() || $creative->offer_id !== $offer->id) {
            abort(403);
        }

        $creative->update([
            'is_active' => !$creative->is_active,
        ]);

        return redirect()->back()->with('success', 'Creative status updated!');
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
