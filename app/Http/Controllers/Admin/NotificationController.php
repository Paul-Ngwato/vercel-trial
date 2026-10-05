<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use App\Models\Photo;
use App\Models\GuestbookMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $wedding = $this->getWedding();
        $notifications = collect();

        // Recent RSVPs
        Rsvp::with('guest')->latest()->take(20)->get()->each(function ($rsvp) use (&$notifications) {
            $notifications->push([
                'type' => 'rsvp',
                'icon' => $rsvp->attendance === 'attending' ? 'bi-check-circle-fill text-green-600' : 'bi-x-circle-fill text-red-500',
                'title' => "RSVP from {$rsvp->guest->name}",
                'detail' => $rsvp->attendance === 'attending'
                    ? "Attending with {$rsvp->guest_count} guest(s)"
                    : 'Not attending',
                'time' => $rsvp->created_at,
                'link' => route('admin.rsvps'),
            ]);
        });

        // Recent Photos
        Photo::latest()->take(20)->get()->each(function ($p) use (&$notifications) {
            $notifications->push([
                'type' => 'photo',
                'icon' => 'bi-camera-fill text-sky-500',
                'title' => "Photo by {$p->uploader_name}",
                'detail' => $p->status === 'pending' ? 'Awaiting approval' : ucfirst($p->status),
                'time' => $p->created_at,
                'link' => route('admin.photos'),
            ]);
        });

        // Recent Guestbook
        GuestbookMessage::latest()->take(20)->get()->each(function ($m) use (&$notifications) {
            $notifications->push([
                'type' => 'guestbook',
                'icon' => 'bi-chat-heart-fill text-pink-500',
                'title' => "Message from {$m->name}",
                'detail' => Str::limit($m->message, 60),
                'time' => $m->created_at,
                'link' => route('admin.guestbook'),
            ]);
        });

        $notifications = $notifications->sortByDesc('time')->take(50);

        return view('admin.notifications', compact('wedding', 'notifications'));
    }
}
