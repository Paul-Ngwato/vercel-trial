<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('wedding_id', $this->getWedding()->id)->orderBy('display_order')->get();
        return view('admin.events', ['events' => $events, 'wedding' => $this->getWedding()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $validated['wedding_id'] = $this->getWedding()->id;
        $validated['status'] = 'active';

        Event::create($validated);

        return back()->with('success', 'Event added.');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $event->update($validated);

        return back()->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return back()->with('success', 'Event deleted.');
    }
}
