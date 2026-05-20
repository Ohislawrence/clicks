<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCreative;
use Illuminate\Support\Facades\Storage;

class CreativeController extends Controller
{
    public function toggle(Offer $offer, OfferCreative $creative)
    {
        if ($creative->offer_id !== $offer->id) {
            abort(404);
        }

        $creative->update([
            'is_active' => !$creative->is_active,
        ]);

        $status = $creative->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Creative \"{$creative->name}\" {$status}.");
    }

    public function destroy(Offer $offer, OfferCreative $creative)
    {
        if ($creative->offer_id !== $offer->id) {
            abort(404);
        }

        if ($creative->file_path) {
            Storage::disk('public')->delete($creative->file_path);
        }

        $creativeName = $creative->name;
        $creative->delete();

        return back()->with('success', "Creative \"{$creativeName}\" deleted.");
    }
}
