<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminMissingEstimatesReport extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($missingGrowers)
    {
        $this->missingGrowers = $missingGrowers;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
   public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('⚠️ Missing Grower Estimates')
            ->line('The following growers have not submitted estimates:')
            ->line(implode(', ', $this->missingGrowers))
            ->action('View Weekly Plans', url('/admin/weekly-oversight'));
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
