<?php

namespace App\Notifications;

use App\Models\Rsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRsvpNotification extends Notification
{
    use Queueable;

    public function __construct(public Rsvp $rsvp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $guest = $this->rsvp->guest;
        $status = $this->rsvp->attendance === 'attending' ? '✅ Attending' : '❌ Not Attending';

        $mail = (new MailMessage)
            ->subject("New RSVP from {$guest->name}")
            ->greeting("New RSVP Received 💒")
            ->line("**{$guest->name}** has responded to your wedding invitation.")
            ->line("**Status:** {$status}")
            ->line("**Supporting:** " . ucfirst($guest->supporting))
            ->line("**Relationship:** " . ucfirst($guest->relationship));

        if ($this->rsvp->guest_count > 1) {
            $mail->line("**Guests:** {$this->rsvp->guest_count} people");
        }

        if ($guest->email) {
            $mail->line("**Email:** {$guest->email}");
        }

        if ($this->rsvp->dietary_requirements) {
            $mail->line("**Dietary:** {$this->rsvp->dietary_requirements}");
        }

        if ($this->rsvp->message) {
            $mail->line("**Message:** {$this->rsvp->message}");
        }

        return $mail
            ->action('View in Dashboard', route('admin.rsvps'))
            ->line('Wedding Digital Hub');
    }
}
