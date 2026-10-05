<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\User;
use App\Notifications\NewRsvpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RsvpController extends Controller
{
    public function index()
    {
        return redirect()->to(route('home') . '#rsvp');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'relationship' => 'required|in:friend,family,colleague,church,neighbor,other',
            'relationship_other' => 'required_if:relationship,other|nullable|string|max:255',
            'supporting' => 'required|in:bride,groom,both',
            'attendance' => 'required|in:attending,not_attending',
            'guest_count' => 'required_if:attendance,attending|integer|min:1|max:10',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'dietary_requirements' => 'nullable|string|max:500',
            'message' => 'nullable|string|max:1000',
        ]);

        // Land back INSIDE the RSVP section with everything the guest typed kept
        if ($validator->fails()) {
            return redirect()->to(route('home') . '#rsvp')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $guest = Guest::create([
            'name' => $validated['name'],
            'relationship' => $validated['relationship'],
            'relationship_other' => $validated['relationship_other'] ?? null,
            'supporting' => $validated['supporting'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        $rsvp = $guest->rsvp()->create([
            'attendance' => $validated['attendance'],
            'guest_count' => $validated['guest_count'] ?? 1,
            'dietary_requirements' => $validated['dietary_requirements'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        // Notify admin
        $this->notifyAdmin(new NewRsvpNotification($rsvp));

        // Back to the RSVP section with a success flag (shown as a classy modal)
        return redirect()
            ->to(route('home') . '#rsvp')
            ->with('rsvp_success', $guest->name);
    }

    protected function notifyAdmin($notification): void
    {
        User::where('role', 'super_admin')->each(fn ($admin) => $admin->notify($notification));
    }
}
