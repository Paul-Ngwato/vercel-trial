<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Rsvp;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::with('rsvp');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('relationship', 'like', "%{$search}%");
            });
        }

        if ($attendance = $request->get('attendance')) {
            $query->whereHas('rsvp', fn ($q) => $q->where('attendance', $attendance));
        }

        $guests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.rsvps', ['guests' => $guests, 'wedding' => $this->getWedding()]);
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('admin.rsvps')->with('success', 'Guest removed.');
    }
}
