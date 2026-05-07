<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\LinkRotationGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartLinkService
{
    /**
     * Select the best link based on rotation strategy and targeting rules
     */
    public function selectLink(array $context): ?AffiliateLink
    {
        $links = $this->getEligibleLinks($context);
        
        if ($links->isEmpty()) {
            return null;
        }
        
        // If only one link, return it
        if ($links->count() === 1) {
            return $links->first();
        }
        
        // Apply rotation strategy
        $rotationGroup = $links->first()->rotationGroup;
        
        if ($rotationGroup && $rotationGroup->is_active) {
            return $this->applyRotationStrategy($links, $rotationGroup);
        }
        
        // Default: random selection
        return $links->random();
    }
    
    /**
     * Get links that match targeting criteria
     */
    protected function getEligibleLinks(array $context): \Illuminate\Database\Eloquent\Collection
    {
        $offerId = $context['offer_id'] ?? null;
        $affiliateId = $context['affiliate_id'] ?? null;
        $country = $context['country'] ?? null;
        $region = $context['region'] ?? null;
        $city = $context['city'] ?? null;
        $device = $context['device'] ?? 'desktop';
        $os = $context['os'] ?? null;
        $browser = $context['browser'] ?? null;
        $currentTime = Carbon::now();
        $currentDay = $currentTime->dayOfWeek; // 0 = Sunday, 6 = Saturday
        
        $query = AffiliateLink::where('is_active', true);
        
        if ($offerId) {
            $query->where('offer_id', $offerId);
        }
        
        if ($affiliateId) {
            $query->where('affiliate_id', $affiliateId);
        }
        
        // Apply geo-targeting
        $query->where(function ($q) use ($country, $region, $city) {
            $q->where('enable_geo_targeting', false)
              ->orWhere(function ($geoQuery) use ($country, $region, $city) {
                  $geoQuery->where('enable_geo_targeting', true);
                  
                  // Check allowed countries
                  if ($country) {
                      $geoQuery->where(function ($cq) use ($country) {
                          $cq->whereNull('allowed_countries')
                             ->orWhereRaw('JSON_CONTAINS(allowed_countries, ?)', [json_encode($country)]);
                      });
                      
                      // Check blocked countries
                      $geoQuery->where(function ($cq) use ($country) {
                          $cq->whereNull('blocked_countries')
                             ->orWhereRaw('NOT JSON_CONTAINS(blocked_countries, ?)', [json_encode($country)]);
                      });
                  }
                  
                  // Check regions and cities similarly
                  if ($region) {
                      $geoQuery->where(function ($rq) use ($region) {
                          $rq->whereNull('allowed_regions')
                             ->orWhereRaw('JSON_CONTAINS(allowed_regions, ?)', [json_encode($region)]);
                      });
                      $geoQuery->where(function ($rq) use ($region) {
                          $rq->whereNull('blocked_regions')
                             ->orWhereRaw('NOT JSON_CONTAINS(blocked_regions, ?)', [json_encode($region)]);
                      });
                  }
                  
                  if ($city) {
                      $geoQuery->where(function ($cityQuery) use ($city) {
                          $cityQuery->whereNull('allowed_cities')
                             ->orWhereRaw('JSON_CONTAINS(allowed_cities, ?)', [json_encode($city)]);
                      });
                      $geoQuery->where(function ($cityQuery) use ($city) {
                          $cityQuery->whereNull('blocked_cities')
                             ->orWhereRaw('NOT JSON_CONTAINS(blocked_cities, ?)', [json_encode($city)]);
                      });
                  }
              });
        });
        
        // Apply device targeting
        $query->where(function ($q) use ($device, $os, $browser) {
            $q->where('enable_device_targeting', false)
              ->orWhere(function ($deviceQuery) use ($device, $os, $browser) {
                  $deviceQuery->where('enable_device_targeting', true);
                  
                  if ($device) {
                      $deviceQuery->where(function ($dq) use ($device) {
                          $dq->whereNull('allowed_devices')
                             ->orWhereRaw('JSON_CONTAINS(allowed_devices, ?)', [json_encode($device)]);
                      });
                  }
                  
                  if ($os) {
                      $deviceQuery->where(function ($oq) use ($os) {
                          $oq->whereNull('allowed_os')
                             ->orWhereRaw('JSON_CONTAINS(allowed_os, ?)', [json_encode($os)]);
                      });
                  }
                  
                  if ($browser) {
                      $deviceQuery->where(function ($bq) use ($browser) {
                          $bq->whereNull('allowed_browsers')
                             ->orWhereRaw('JSON_CONTAINS(allowed_browsers, ?)', [json_encode($browser)]);
                      });
                  }
              });
        });
        
        // Apply time-based scheduling
        $query->where(function ($q) use ($currentTime, $currentDay) {
            $q->where('enable_schedule', false)
              ->orWhere(function ($scheduleQuery) use ($currentTime, $currentDay) {
                  $scheduleQuery->where('enable_schedule', true);
                  
                  // Check time range
                  $scheduleQuery->where(function ($tq) use ($currentTime) {
                      $tq->whereNull('active_start_time')
                         ->orWhere(function ($timeQuery) use ($currentTime) {
                             $timeQuery->whereTime('active_start_time', '<=', $currentTime->format('H:i:s'))
                                      ->whereTime('active_end_time', '>=', $currentTime->format('H:i:s'));
                         });
                  });
                  
                  // Check active days
                  $scheduleQuery->where(function ($dq) use ($currentDay) {
                      $dq->whereNull('active_days')
                         ->orWhereRaw('JSON_CONTAINS(active_days, ?)', [json_encode($currentDay)]);
                  });
              });
        });
        
        return $query->with(['rotationGroup', 'offer'])->get();
    }
    
    /**
     * Apply rotation strategy to select a link
     */
    protected function applyRotationStrategy($links, LinkRotationGroup $group): AffiliateLink
    {
        switch ($group->rotation_strategy) {
            case 'sequential':
                return $this->sequentialRotation($links);
                
            case 'weighted':
                return $this->weightedRotation($links);
                
            case 'performance':
                return $this->performanceBasedRotation($links);
                
            case 'random':
            default:
                return $links->random();
        }
    }
    
    /**
     * Sequential rotation (round-robin)
     */
    protected function sequentialRotation($links): AffiliateLink
    {
        // Sort by last_rotated_at and rotation_priority
        $sorted = $links->sortBy([
            ['last_rotated_at', 'asc'],
            ['rotation_priority', 'desc'],
        ]);
        
        return $sorted->first();
    }
    
    /**
     * Weighted rotation based on rotation_weight
     */
    protected function weightedRotation($links): AffiliateLink
    {
        $totalWeight = $links->sum('rotation_weight');
        $random = rand(1, $totalWeight);
        $currentWeight = 0;
        
        foreach ($links as $link) {
            $currentWeight += $link->rotation_weight;
            if ($random <= $currentWeight) {
                return $link;
            }
        }
        
        return $links->first();
    }
    
    /**
     * Performance-based rotation (highest CR gets more traffic)
     */
    protected function performanceBasedRotation($links): AffiliateLink
    {
        // Filter links with minimum clicks for statistical significance
        $eligibleLinks = $links->filter(function ($link) {
            return $link->rotation_clicks >= 10;
        });
        
        if ($eligibleLinks->isEmpty()) {
            // Not enough data, use random
            return $links->random();
        }
        
        // Sort by conversion rate
        $sorted = $eligibleLinks->sortByDesc('rotation_cr');
        
        // 70% chance for best performer, 20% for second, 10% for others
        $rand = rand(1, 100);
        
        if ($rand <= 70) {
            return $sorted->first();
        } elseif ($rand <= 90 && $sorted->count() > 1) {
            return $sorted->skip(1)->first();
        } else {
            return $sorted->random();
        }
    }
    
    /**
     * Record link rotation and update stats
     */
    public function recordRotation(AffiliateLink $link): void
    {
        $link->increment('rotation_clicks');
        $link->update(['last_rotated_at' => now()]);
        
        // Update rotation group stats
        if ($link->rotationGroup) {
            $link->rotationGroup->increment('total_clicks');
        }
    }
    
    /**
     * Update rotation stats after conversion
     */
    public function recordConversion(AffiliateLink $link, float $revenue): void
    {
        $link->increment('rotation_conversions');
        
        // Recalculate conversion rate
        if ($link->rotation_clicks > 0) {
            $cr = ($link->rotation_conversions / $link->rotation_clicks) * 100;
            $link->update(['rotation_cr' => round($cr, 2)]);
        }
        
        // Update rotation group stats
        if ($link->rotationGroup) {
            $group = $link->rotationGroup;
            $group->increment('total_conversions');
            $group->increment('total_revenue', $revenue);
            
            // Recalculate group metrics
            if ($group->total_clicks > 0) {
                $groupCr = ($group->total_conversions / $group->total_clicks) * 100;
                $groupEpc = $group->total_revenue / $group->total_clicks;
                
                $group->update([
                    'group_cr' => round($groupCr, 2),
                    'group_epc' => round($groupEpc, 2),
                ]);
            }
            
            // Check if auto-optimization is enabled
            if ($group->auto_optimize && $group->total_clicks >= $group->optimization_threshold_clicks) {
                $this->optimizeRotationGroup($group);
            }
        }
    }
    
    /**
     * Optimize rotation group based on performance
     */
    protected function optimizeRotationGroup(LinkRotationGroup $group): void
    {
        $links = $group->links()->where('is_active', true)->get();
        
        if ($links->count() < 2) {
            return;
        }
        
        // Get links with enough data
        $eligibleLinks = $links->filter(fn($link) => $link->rotation_clicks >= 50);
        
        if ($eligibleLinks->isEmpty()) {
            return;
        }
        
        // Calculate average CR
        $avgCr = $eligibleLinks->avg('rotation_cr');
        
        // Disable underperforming links (CR < 50% of average)
        foreach ($eligibleLinks as $link) {
            if ($link->rotation_cr < ($avgCr * 0.5)) {
                $link->update(['is_active' => false]);
            }
        }
        
        $group->update(['last_optimized_at' => now()]);
    }
    
    /**
     * Create a rotation group
     */
    public function createRotationGroup(array $data): LinkRotationGroup
    {
        return LinkRotationGroup::create($data);
    }
    
    /**
     * Add link to rotation group
     */
    public function addLinkToGroup(AffiliateLink $link, LinkRotationGroup $group): void
    {
        $link->update([
            'rotation_group_id' => $group->id,
            'enable_rotation' => true,
        ]);
    }
    
    /**
     * Get rotation group stats
     */
    public function getGroupStats(LinkRotationGroup $group): array
    {
        $links = $group->links()->where('is_active', true)->get();
        
        return [
            'group_id' => $group->id,
            'group_name' => $group->name,
            'total_links' => $links->count(),
            'total_clicks' => $group->total_clicks,
            'total_conversions' => $group->total_conversions,
            'total_revenue' => $group->total_revenue,
            'group_cr' => $group->group_cr,
            'group_epc' => $group->group_epc,
            'rotation_strategy' => $group->rotation_strategy,
            'last_optimized' => $group->last_optimized_at?->diffForHumans(),
            'links' => $links->map(function ($link) {
                return [
                    'id' => $link->id,
                    'tracking_code' => $link->tracking_code,
                    'rotation_clicks' => $link->rotation_clicks,
                    'rotation_conversions' => $link->rotation_conversions,
                    'rotation_cr' => $link->rotation_cr,
                    'rotation_weight' => $link->rotation_weight,
                    'rotation_priority' => $link->rotation_priority,
                    'last_rotated' => $link->last_rotated_at?->diffForHumans(),
                ];
            }),
        ];
    }
}
