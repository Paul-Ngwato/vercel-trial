<?php

namespace App\Notifications;

use App\Models\GuestbookMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGuestbookMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public GuestbookMessage $message) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New message from {$this->message->name}")
            ->greeting("New Guestbook Message 💌")
            ->line("**{$this->message->name}** has left a message for the couple.")
            ->line("**Message:** \"{$this->message->message}\"")
            ->line("This message is awaiting your approval.")
            ->action('Review Message', route('admin.guestbook'))
            ->line('Wedding Digital Hub');
    }
}
