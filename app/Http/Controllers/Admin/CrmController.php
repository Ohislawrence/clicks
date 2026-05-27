<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Mail\LeadMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->withCount('activities')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/CRM/Index', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create($validated);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'status_change',
            'description' => 'Lead created manually',
        ]);

        return redirect()->route('admin.crm.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        return Inertia::render('Admin/CRM/Show', [
            'lead' => $lead->load(['activities.user', 'assignedTo']),
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'required|string|in:new,contacted,interested,converted,rejected',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        if ($oldStatus !== $lead->status) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'description' => "Status changed from {$oldStatus} to {$lead->status}",
            ]);
        }

        return back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.crm.index')->with('success', 'Lead deleted successfully.');
    }

    public function sendEmail(Request $request, Lead $lead)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Mail::to($lead->email)->send(new LeadMail($lead, $request->subject, $request->content));

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'email',
            'description' => "Personalized email sent: {$request->subject}",
            'metadata' => [
                'subject' => $request->subject,
                'content' => $request->content,
            ],
        ]);

        $lead->update(['last_contacted_at' => now()]);

        return back()->with('success', 'Email sent successfully.');
    }

    public function bulkEmail(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $leads = Lead::whereIn('id', $request->lead_ids)->get();

        foreach ($leads as $lead) {
            Mail::to($lead->email)->later(now()->addSeconds(rand(1, 60)), new LeadMail($lead, $request->subject, $request->content));

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'email',
                'description' => "Bulk email sent: {$request->subject}",
                'metadata' => [
                    'subject' => $request->subject,
                    'is_bulk' => true,
                ],
            ]);

            $lead->update(['last_contacted_at' => now()]);
        }

        return back()->with('success', 'Bulk emails queued for sending.');
    }

    public function addNote(Request $request, Lead $lead)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'note',
            'description' => $request->note,
        ]);

        return back()->with('success', 'Note added successfully.');
    }
}
