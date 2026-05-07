<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyPerformanceSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $stats;
    public string $userType; // 'affiliate' or 'advertiser'

    /**
     * Create a new notification instance.
     */
    public function __construct(array $stats, string $userType)
    {
        $this->stats = $stats;
        $this->userType = $userType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->userType === 'affiliate') {
            return $this->affiliateEmail($notifiable);
        } else {
            return $this->advertiserEmail($notifiable);
        }
    }

    /**
     * Affiliate weekly summary email
     */
    protected function affiliateEmail(object $notifiable): MailMessage
    {
        $stats = $this->stats;
        $trend = $this->getTrendEmoji($stats['earnings_change'] ?? 0);
        
        return (new MailMessage)
            ->subject('📊 Your Weekly Performance Summary')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Here\'s how you performed this week:')
            ->line('')
            ->line('**💰 Earnings:**')
            ->line('- **This Week**: $' . number_format($stats['earnings'], 2))
            ->line('- **Last Week**: $' . number_format($stats['previous_earnings'], 2))
            ->line('- **Change**: ' . $trend . ' ' . number_format(abs($stats['earnings_change'] ?? 0), 1) . '%')
            ->line('')
            ->line('**📈 Conversions:**')
            ->line('- **Total Conversions**: ' . number_format($stats['conversions']))
            ->line('- **Total Clicks**: ' . number_format($stats['clicks']))
            ->line('- **Conversion Rate**: ' . number_format($stats['conversion_rate'], 2) . '%')
            ->line('')
            ->line('**🏆 Top Performing Offers:**')
            ->line($this->formatTopOffers($stats['top_offers'] ?? []))
            ->line('')
            ->line('**🚀 Quick Tips:**')
            ->line($this->getAffiliateTip($stats))
            ->action('View Full Report', route('affiliate.dashboard'))
            ->line('Keep up the great work!');
    }

    /**
     * Advertiser weekly summary email
     */
    protected function advertiserEmail(object $notifiable): MailMessage
    {
        $stats = $this->stats;
        $trend = $this->getTrendEmoji($stats['conversions_change'] ?? 0);
        
        return (new MailMessage)
            ->subject('📊 Your Weekly Campaign Performance')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Here\'s your campaign performance for this week:')
            ->line('')
            ->line('**💸 Revenue & Spend:**')
            ->line('- **Total Revenue**: $' . number_format($stats['revenue'], 2))
            ->line('- **Total Spent**: $' . number_format($stats['spent'], 2))
            ->line('- **ROI**: ' . number_format($stats['roi'], 1) . '%')
            ->line('')
            ->line('**🎯 Conversions:**')
            ->line('- **Total Conversions**: ' . number_format($stats['conversions']))
            ->line('- **Last Week**: ' . number_format($stats['previous_conversions']))
            ->line('- **Change**: ' . $trend . ' ' . number_format(abs($stats['conversions_change'] ?? 0), 1) . '%')
            ->line('')
            ->line('**🔥 Top Performing Offers:**')
            ->line($this->formatTopOffers($stats['top_offers'] ?? []))
            ->line('')
            ->line('**💡 Insights:**')
            ->line($this->getAdvertiserInsight($stats))
            ->action('View Full Analytics', route('advertiser.dashboard'))
            ->line('Optimize your campaigns for better results!');
    }

    /**
     * Get trend emoji based on percentage change
     */
    protected function getTrendEmoji(float $change): string
    {
        if ($change > 0) return '📈';
        if ($change < 0) return '📉';
        return '➡️';
    }

    /**
     * Format top offers list
     */
    protected function formatTopOffers(array $offers): string
    {
        if (empty($offers)) {
            return '- No data available';
        }
        
        $formatted = [];
        foreach (array_slice($offers, 0, 3) as $offer) {
            $formatted[] = '- ' . $offer['name'] . ': ' . number_format($offer['conversions']) . ' conversions';
        }
        
        return implode("\n", $formatted);
    }

    /**
     * Get personalized tip for affiliate
     */
    protected function getAffiliateTip(array $stats): string
    {
        if (($stats['conversion_rate'] ?? 0) < 1) {
            return 'Your conversion rate is low. Try testing different traffic sources or improving your content quality.';
        } elseif (($stats['clicks'] ?? 0) < 100) {
            return 'Focus on driving more traffic to your links. Consider expanding to more platforms.';
        } else {
            return 'Great performance! Consider applying for higher-paying exclusive offers.';
        }
    }

    /**
     * Get personalized insight for advertiser
     */
    protected function getAdvertiserInsight(array $stats): string
    {
        if (($stats['roi'] ?? 0) < 100) {
            return 'Your ROI is below target. Consider optimizing your payouts or improving your offer quality.';
        } elseif (($stats['conversions'] ?? 0) > ($stats['previous_conversions'] ?? 0)) {
            return 'Conversions are trending up! Keep your budgets sufficient to maintain momentum.';
        } else {
            return 'Review your top-performing affiliates and consider recruiting similar traffic sources.';
        }
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'weekly_summary',
            'title' => 'Weekly Performance Summary',
            'message' => $this->userType === 'affiliate' 
                ? 'You earned $' . number_format($this->stats['earnings'], 2) . ' this week with ' . number_format($this->stats['conversions']) . ' conversions.'
                : 'Your campaigns generated ' . number_format($this->stats['conversions']) . ' conversions this week.',
            'action_url' => $this->userType === 'affiliate' ? route('affiliate.dashboard') : route('advertiser.dashboard'),
            'action_text' => 'View Dashboard',
            'stats' => $this->stats,
            'user_type' => $this->userType,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
