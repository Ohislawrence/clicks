<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlacklistHitNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $violations;
    public array $clickData;
    public string $severity;
    public bool $wasBlocked;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $violations, array $clickData, string $severity, bool $wasBlocked)
    {
        $this->violations = $violations;
        $this->clickData = $clickData;
        $this->severity = $severity;
        $this->wasBlocked = $wasBlocked;
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
        $message = (new MailMessage)
            ->subject('⚠️ Blacklist Hit Alert - ' . strtoupper($this->severity) . ' Severity')
            ->greeting('Blacklist Violation Detected!')
            ->line($this->wasBlocked 
                ? '🚫 A click was **BLOCKED** due to blacklist violations.' 
                : '⚡ A click triggered blacklist rules but was not blocked.')
            ->line('**Violation Details:**');

        // Add each violation
        foreach ($this->violations as $violation) {
            $message->line("- **{$violation['type']}**: {$violation['matched_value']} ({$violation['reason']})");
        }

        $message->line('')
            ->line('**Click Information:**')
            ->line("- **IP Address**: {$this->clickData['ip']}")
            ->line("- **User Agent**: " . ($this->clickData['user_agent'] ?? 'N/A'))
            ->line("- **Referrer**: " . ($this->clickData['referrer'] ?? 'N/A'))
            ->line("- **Country**: " . ($this->clickData['country_code'] ?? 'N/A'))
            ->line("- **Time**: " . now()->format('Y-m-d H:i:s'));

        if (isset($this->clickData['offer_id'])) {
            $message->line("- **Offer ID**: {$this->clickData['offer_id']}");
        }

        if (isset($this->clickData['affiliate_id'])) {
            $message->line("- **Affiliate ID**: {$this->clickData['affiliate_id']}");
        }

        $message->line('')
            ->line('**Action Required:**')
            ->line($this->severity === 'critical' 
                ? 'This is a **CRITICAL** violation. Please review immediately.' 
                : 'Review this violation and update blacklist rules if needed.')
            ->action('View Blacklist Dashboard', route('admin.blacklists.index'))
            ->line('Stay vigilant to maintain traffic quality!');

        // Set priority based on severity
        if (in_array($this->severity, ['high', 'critical'])) {
            $message->priority(\Illuminate\Notifications\Messages\MailMessage::HIGH);
        }

        return $message;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'severity' => $this->severity,
            'was_blocked' => $this->wasBlocked,
            'violation_count' => count($this->violations),
            'violations' => $this->violations,
            'click_data' => $this->clickData,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'severity' => $this->severity,
            'was_blocked' => $this->wasBlocked,
            'violation_count' => count($this->violations),
            'violations' => $this->violations,
            'click_data' => $this->clickData,
        ];
    }
}
