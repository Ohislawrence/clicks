<?php

namespace App\Notifications;

use App\Models\LmsCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCoursePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public LmsCourse $course;

    public function __construct(LmsCourse $course)
    {
        $this->course = $course;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lessonCount = $this->course->publishedLessons()->count();

        return (new MailMessage)
            ->subject('🎓 New Free Course: ' . $this->course->title)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('A brand-new free course has just been published on the ' . config('app.name') . ' Learning Center.')
            ->line('')
            ->line('**' . $this->course->title . '**')
            ->line($this->course->description)
            ->line('')
            ->line('• **Level:** ' . ucfirst($this->course->level))
            ->line('• **Lessons:** ' . $lessonCount . ($lessonCount === 1 ? ' lesson' : ' lessons'))
            ->line('• **Duration:** ' . ($this->course->duration_minutes ?? 0) . ' minutes')
            ->line('• **Price:** Free — always')
            ->line('')
            ->action('View Course', route('learning.show', $this->course->slug))
            ->line('Start learning today and boost your ' . config('app.name') . ' results!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'new_course_published',
            'title'       => 'New Course: ' . $this->course->title,
            'message'     => 'A new free course "' . $this->course->title . '" has been published. Enroll and start learning!',
            'action_url'  => route('learning.show', $this->course->slug),
            'action_text' => 'View Course',
            'course_id'   => $this->course->id,
            'course_slug' => $this->course->slug,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
