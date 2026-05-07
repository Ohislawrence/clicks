<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Services\BlacklistService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlacklistController extends Controller
{
    protected BlacklistService $blacklistService;

    public function __construct(BlacklistService $blacklistService)
    {
        $this->blacklistService = $blacklistService;
    }

    /**
     * Display blacklist entries
     */
    public function index(Request $request)
    {
        $query = Blacklist::with(['creator', 'updater', 'offer'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('value', 'like', "%{$request->search}%")
                  ->orWhere('reason', 'like', "%{$request->search}%");
            });
        }

        $blacklists = $query->paginate(50);
        $stats = $this->blacklistService->getStats();

        return Inertia::render('Admin/Blacklists/Index', [
            'blacklists' => $blacklists,
            'stats' => $stats,
            'filters' => $request->only(['type', 'scope', 'severity', 'is_active', 'search']),
        ]);
    }

    /**
     * Store new blacklist entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:ip,ip_range,user_agent,referrer,device_fingerprint,country,asn',
            'value' => 'required|string|max:500',
            'match_type' => 'required|in:exact,contains,regex,wildcard',
            'scope' => 'required|in:global,offer,affiliate',
            'scope_id' => 'nullable|exists:users,id',
            'offer_id' => 'nullable|exists:offers,id',
            'is_active' => 'boolean',
            'severity' => 'required|in:low,medium,high,critical',
            'action' => 'required|in:block,flag,reduce_quality',
            'quality_penalty' => 'integer|min:0|max:100',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $validated['created_by'] = auth()->id();

        $blacklist = $this->blacklistService->add($validated);

        return redirect()->back()->with('success', 'Blacklist entry created successfully.');
    }

    /**
     * Update blacklist entry
     */
    public function update(Request $request, Blacklist $blacklist)
    {
        $validated = $request->validate([
            'value' => 'string|max:500',
            'match_type' => 'in:exact,contains,regex,wildcard',
            'is_active' => 'boolean',
            'severity' => 'in:low,medium,high,critical',
            'action' => 'in:block,flag,reduce_quality',
            'quality_penalty' => 'integer|min:0|max:100',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $validated['updated_by'] = auth()->id();

        $this->blacklistService->update($blacklist, $validated);

        return redirect()->back()->with('success', 'Blacklist entry updated successfully.');
    }

    /**
     * Delete blacklist entry
     */
    public function destroy(Blacklist $blacklist)
    {
        $this->blacklistService->delete($blacklist);

        return redirect()->back()->with('success', 'Blacklist entry deleted successfully.');
    }

    /**
     * Bulk delete
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blacklists,id',
        ]);

        $blacklists = Blacklist::whereIn('id', $validated['ids'])->get();
        
        foreach ($blacklists as $blacklist) {
            $this->blacklistService->delete($blacklist);
        }

        return redirect()->back()->with('success', count($validated['ids']) . ' entries deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Blacklist $blacklist)
    {
        $this->blacklistService->update($blacklist, [
            'is_active' => !$blacklist->is_active,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Import from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $entries = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // Skip header row
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 5) {
                    $entries[] = [
                        'type' => $row[0],
                        'value' => $row[1],
                        'match_type' => $row[2] ?? 'exact',
                        'severity' => $row[3] ?? 'medium',
                        'action' => $row[4] ?? 'block',
                        'reason' => $row[5] ?? null,
                        'scope' => $row[6] ?? 'global',
                        'is_active' => true,
                        'quality_penalty' => (int) ($row[7] ?? 50),
                    ];
                }
            }
            
            fclose($handle);
        }

        $result = $this->blacklistService->import($entries, auth()->id());

        return redirect()->back()->with('success', 
            "Import complete: {$result['imported']} imported, {$result['failed']} failed."
        );
    }

    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        $query = Blacklist::with(['creator', 'offer']);

        // Apply same filters as index
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        $blacklists = $query->get();

        $csv = "Type,Value,Match Type,Scope,Severity,Action,Quality Penalty,Reason,Hit Count,Is Active,Created At,Created By\n";

        foreach ($blacklists as $blacklist) {
            $csv .= implode(',', [
                $blacklist->type,
                '"' . str_replace('"', '""', $blacklist->value) . '"',
                $blacklist->match_type,
                $blacklist->scope,
                $blacklist->severity,
                $blacklist->action,
                $blacklist->quality_penalty,
                '"' . str_replace('"', '""', $blacklist->reason ?? '') . '"',
                $blacklist->hit_count,
                $blacklist->is_active ? 'Yes' : 'No',
                $blacklist->created_at->format('Y-m-d H:i:s'),
                $blacklist->creator->name ?? 'Unknown',
            ]) . "\n";
        }

        $filename = 'blacklist_export_' . now()->format('Y-m-d_His') . '.csv';

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Test if value would be blacklisted
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:ip,user_agent,referrer,device_fingerprint,country',
            'value' => 'required|string',
            'offer_id' => 'nullable|exists:offers,id',
            'affiliate_id' => 'nullable|exists:users,id',
        ]);

        $result = match ($validated['type']) {
            'ip' => $this->blacklistService->checkIp(
                $validated['value'],
                $validated['offer_id'] ?? null,
                $validated['affiliate_id'] ?? null
            ),
            'user_agent' => $this->blacklistService->checkUserAgent(
                $validated['value'],
                $validated['offer_id'] ?? null,
                $validated['affiliate_id'] ?? null
            ),
            'referrer' => $this->blacklistService->checkReferrer(
                $validated['value'],
                $validated['offer_id'] ?? null,
                $validated['affiliate_id'] ?? null
            ),
            'device_fingerprint' => $this->blacklistService->checkDeviceFingerprint(
                $validated['value'],
                $validated['offer_id'] ?? null,
                $validated['affiliate_id'] ?? null
            ),
            'country' => $this->blacklistService->checkCountry(
                $validated['value'],
                $validated['offer_id'] ?? null,
                $validated['affiliate_id'] ?? null
            ),
            default => ['is_blacklisted' => false],
        };

        return response()->json($result);
    }

    /**
     * Get statistics
     */
    public function stats()
    {
        $stats = $this->blacklistService->getStats();
        return response()->json($stats);
    }
}
