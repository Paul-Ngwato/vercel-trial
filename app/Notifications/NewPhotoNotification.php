<?php

namespace App\Notifications;

use App\Models\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPhotoNotification extends Notification
{
    use Queueable;

    public function __construct(public Photo $photo) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New photo uploaded by {$this->photo->uploader_name}")
            ->greeting("New Photo Uploaded 📸")
            ->line("**{$this->photo->uploader_name}** has uploaded a photo.")
            ->line("The photo is awaiting your approval.")
            ->action('Review Photo', route('admin.photos'))
            ->line('Wedding Digital Hub');
    }
}
