<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\Commission;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ExportService
{
    /**
     * Export clicks data to CSV
     */
    public function exportClicks(array $filters = []): string
    {
        $query = Click::query()->with(['affiliateLink', 'offer', 'affiliate']);
        
        $this->applyFilters($query, $filters);
        
        $clicks = $query->get();
        
        $csv = $this->generateCsvHeader([
            'Date',
            'Affiliate',
            'Offer',
            'IP Address',
            'Country',
            'City',
            'Device',
            'Browser',
            'OS',
            'Quality Score',
            'Risk Level',
            'Is Valid',
            'Is Converted',
        ]);
        
        foreach ($clicks as $click) {
            $csv .= $this->generateCsvRow([
                $click->created_at->format('Y-m-d H:i:s'),
                $click->affiliate->name ?? 'N/A',
                $click->offer->name ?? 'N/A',
                $click->ip_address,
                $click->country_name ?? $click->country_code,
                $click->city ?? 'N/A',
                $click->device_type ?? 'N/A',
                $click->browser ?? 'N/A',
                $click->os ?? 'N/A',
                $click->quality_score ?? 'N/A',
                $click->risk_level ?? 'N/A',
                $click->is_valid ? 'Yes' : 'No',
                $click->is_converted ? 'Yes' : 'No',
            ]);
        }
        
        return $csv;
    }
    
    /**
     * Export conversions data to CSV
     */
    public function exportConversions(array $filters = []): string
    {
        $query = Conversion::query()->with(['affiliate', 'offer', 'click']);
        
        $this->applyFilters($query, $filters);
        
        $conversions = $query->get();
        
        $csv = $this->generateCsvHeader([
            'Date',
            'Transaction ID',
            'Affiliate',
            'Offer',
            'Value',
            'Commission',
            'Status',
            'Country',
            'Device',
            'Quality Score',
            'Time to Convert (seconds)',
        ]);
        
        foreach ($conversions as $conversion) {
            $timeToConvert = $conversion->click 
                ? $conversion->created_at->diffInSeconds($conversion->click->created_at)
                : 'N/A';
                
            $csv .= $this->generateCsvRow([
                $conversion->created_at->format('Y-m-d H:i:s'),
                $conversion->transaction_id ?? 'N/A',
                $conversion->affiliate->name ?? 'N/A',
                $conversion->offer->name ?? 'N/A',
                '₦' . number_format($conversion->conversion_value, 2),
                '₦' . number_format($conversion->commission_amount, 2),
                ucfirst($conversion->status),
                $conversion->click->country_name ?? 'N/A',
                $conversion->click->device_type ?? 'N/A',
                $conversion->click->quality_score ?? 'N/A',
                $timeToConvert,
            ]);
        }
        
        return $csv;
    }
    
    /**
     * Export commissions data to CSV
     */
    public function exportCommissions(array $filters = []): string
    {
        $query = Commission::query()->with(['affiliate', 'offer', 'conversion']);
        
        $this->applyFilters($query, $filters);
        
        $commissions = $query->get();
        
        $csv = $this->generateCsvHeader([
            'Date',
            'Affiliate',
            'Offer',
            'Commission Type',
            'Base Amount',
            'Tier Bonus',
            'Platform Fee',
            'Final Amount',
            'Status',
            'Paid At',
        ]);
        
        foreach ($commissions as $commission) {
            $csv .= $this->generateCsvRow([
                $commission->created_at->format('Y-m-d H:i:s'),
                $commission->affiliate->name ?? 'N/A',
                $commission->offer->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $commission->commission_type)),
                '₦' . number_format($commission->base_amount, 2),
                '₦' . number_format($commission->tier_bonus ?? 0, 2),
                '₦' . number_format($commission->platform_fee ?? 0, 2),
                '₦' . number_format($commission->final_amount, 2),
                ucfirst($commission->status),
                $commission->paid_at ? $commission->paid_at->format('Y-m-d H:i:s') : 'Pending',
            ]);
        }
        
        return $csv;
    }
    
    /**
     * Export report stats to CSV
     */
    public function exportReportStats(array $stats): string
    {
        $csv = "# Performance Report\n";
        $csv .= "# Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // Period Info
        $csv .= $this->generateCsvHeader(['Period', 'Value']);
        $csv .= $this->generateCsvRow(['From', $stats['period']['from'] ?? 'N/A']);
        $csv .= $this->generateCsvRow(['To', $stats['period']['to'] ?? 'N/A']);
        $csv .= $this->generateCsvRow(['Days', $stats['period']['days'] ?? 'N/A']);
        $csv .= "\n";
        
        // Clicks
        $csv .= "# Clicks\n";
        $csv .= $this->generateCsvHeader(['Metric', 'Value']);
        $csv .= $this->generateCsvRow(['Total Clicks', $stats['clicks']['total'] ?? 0]);
        $csv .= $this->generateCsvRow(['Valid Clicks', $stats['clicks']['valid'] ?? 0]);
        $csv .= $this->generateCsvRow(['Invalid Clicks', $stats['clicks']['invalid'] ?? 0]);
        $csv .= $this->generateCsvRow(['Fraud Rate (%)', $stats['clicks']['fraud_rate'] ?? 0]);
        $csv .= "\n";
        
        // Conversions
        $csv .= "# Conversions\n";
        $csv .= $this->generateCsvHeader(['Metric', 'Value']);
        $csv .= $this->generateCsvRow(['Total Conversions', $stats['conversions']['total'] ?? 0]);
        $csv .= $this->generateCsvRow(['Approved', $stats['conversions']['approved'] ?? 0]);
        $csv .= $this->generateCsvRow(['Pending', $stats['conversions']['pending'] ?? 0]);
        $csv .= $this->generateCsvRow(['Conversion Rate (%)', $stats['conversions']['rate'] ?? 0]);
        $csv .= "\n";
        
        // Revenue
        $csv .= "# Revenue\n";
        $csv .= $this->generateCsvHeader(['Metric', 'Value']);
        $csv .= $this->generateCsvRow(['Total Revenue', '₦' . number_format($stats['revenue']['total'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['Average Order Value', '₦' . number_format($stats['revenue']['average_order_value'] ?? 0, 2)]);
        $csv .= "\n";
        
        // Commissions
        $csv .= "# Commissions\n";
        $csv .= $this->generateCsvHeader(['Metric', 'Value']);
        $csv .= $this->generateCsvRow(['Total Commissions', '₦' . number_format($stats['commissions']['total'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['Approved', '₦' . number_format($stats['commissions']['approved'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['Pending', '₦' . number_format($stats['commissions']['pending'] ?? 0, 2)]);
        $csv .= "\n";
        
        // Performance Metrics
        $csv .= "# Performance Metrics\n";
        $csv .= $this->generateCsvHeader(['Metric', 'Value']);
        $csv .= $this->generateCsvRow(['EPC (Earnings Per Click)', '₦' . number_format($stats['performance']['epc'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['EPL (Earnings Per Lead)', '₦' . number_format($stats['performance']['epl'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['ROI (%)', number_format($stats['performance']['roi'] ?? 0, 2)]);
        $csv .= $this->generateCsvRow(['CR (Conversion Rate %)', number_format($stats['performance']['cr'] ?? 0, 2)]);
        
        return $csv;
    }
    
    /**
     * Export daily stats to CSV
     */
    public function exportDailyStats(array $dailyStats): string
    {
        $csv = $this->generateCsvHeader([
            'Date',
            'Clicks',
            'Conversions',
            'Revenue',
            'Commissions',
            'CR (%)',
            'EPC',
        ]);
        
        foreach ($dailyStats as $day) {
            $csv .= $this->generateCsvRow([
                $day['date'],
                $day['clicks'] ?? 0,
                $day['conversions'] ?? 0,
                '₦' . number_format($day['revenue'] ?? 0, 2),
                '₦' . number_format($day['commissions'] ?? 0, 2),
                number_format($day['cr'] ?? 0, 2),
                '₦' . number_format($day['epc'] ?? 0, 2),
            ]);
        }
        
        return $csv;
    }
    
    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }
        
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to']));
        }
        
        if (!empty($filters['user_id'])) {
            if ($query->getModel()->getTable() === 'clicks') {
                $query->where('affiliate_id', $filters['user_id']);
            } else {
                $query->where('affiliate_id', $filters['user_id']);
            }
        }
        
        if (!empty($filters['offer_id'])) {
            $query->where('offer_id', $filters['offer_id']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }
    
    /**
     * Generate CSV header row
     */
    protected function generateCsvHeader(array $columns): string
    {
        return implode(',', array_map(function ($col) {
            return '"' . str_replace('"', '""', $col) . '"';
        }, $columns)) . "\n";
    }
    
    /**
     * Generate CSV data row
     */
    protected function generateCsvRow(array $data): string
    {
        return implode(',', array_map(function ($value) {
            return '"' . str_replace('"', '""', $value) . '"';
        }, $data)) . "\n";
    }
    
    /**
     * Get appropriate filename for export
     */
    public function getExportFilename(string $type): string
    {
        $timestamp = now()->format('Y-m-d_His');
        return "dealsintel_{$type}_{$timestamp}.csv";
    }
}
