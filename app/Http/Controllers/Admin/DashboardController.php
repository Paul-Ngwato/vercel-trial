<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use App\Models\Photo;
use App\Models\GuestbookMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $wedding = $this->getWedding();

        $stats = [
            'total_rsvps' => Rsvp::count(),
            'attending' => Rsvp::where('attendance', 'attending')->count(),
            'not_attending' => Rsvp::where('attendance', 'not_attending')->count(),
            'total_guests' => Rsvp::where('attendance', 'attending')->sum('guest_count'),
            'photos_approved' => Photo::approved()->count(),
            'photos_pending' => Photo::pending()->count(),
            'messages_approved' => GuestbookMessage::approved()->count(),
            'messages_pending' => GuestbookMessage::pending()->count(),
        ];

        $recentActivity = collect();

        // Recent RSVPs
        Rsvp::with('guest')->latest()->take(5)->get()->each(function ($rsvp) use (&$recentActivity) {
            $recentActivity->push([
                'icon' => $rsvp->attendance === 'attending' ? 'bi-check-circle-fill text-green-500' : 'bi-x-circle-fill text-red-500',
                'text' => "{$rsvp->guest->name} " . ($rsvp->attendance === 'attending' ? 'confirmed attendance' : 'cannot attend'),
                'time' => $rsvp->created_at->diffForHumans(),
                'ts' => $rsvp->created_at->timestamp,
            ]);
        });

        // Recent photos
        Photo::latest()->take(5)->get()->each(function ($p) use (&$recentActivity) {
            $recentActivity->push([
                'icon' => 'bi-camera-fill text-sky-500',
                'text' => "{$p->uploader_name} shared a photo" . ($p->status === 'pending' ? ' — awaiting review' : ''),
                'time' => $p->created_at->diffForHumans(),
                'ts' => $p->created_at->timestamp,
            ]);
        });

        // Recent wishes
        GuestbookMessage::latest()->take(5)->get()->each(function ($m) use (&$recentActivity) {
            $recentActivity->push([
                'icon' => 'bi-chat-heart-fill text-pink-500',
                'text' => "{$m->name} left a well wish" . ($m->status === 'pending' ? ' — awaiting review' : ''),
                'time' => $m->created_at->diffForHumans(),
                'ts' => $m->created_at->timestamp,
            ]);
        });

        $recentActivity = $recentActivity->sortByDesc('ts')->take(8)->values();

        return view('admin.dashboard', compact('wedding', 'stats', 'recentActivity'));
    }
}
