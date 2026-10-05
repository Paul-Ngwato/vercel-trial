<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoryMilestone;
use App\Models\StoryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function index()
    {
        $milestones = StoryMilestone::with('photos')
            ->where('wedding_id', $this->getWedding()->id)
            ->orderBy('display_order')
            ->get();

        return view('admin.story', ['milestones' => $milestones, 'wedding' => $this->getWedding()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'images' => 'nullable|array|max:6',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $milestone = StoryMilestone::create([
            'wedding_id' => $this->getWedding()->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'display_order' => $validated['display_order'] ?? 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $milestone->photos()->create([
                    'file_path' => $file->store('story', 'public'),
                    'display_order' => $index,
                ]);
            }
        }

        return back()->with('success', 'Story milestone added.');
    }

    public function update(Request $request, StoryMilestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'images' => 'nullable|array|max:6',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $milestone->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            // Only touch the order if the field was actually submitted
            'display_order' => $request->filled('display_order')
                ? $validated['display_order']
                : $milestone->display_order,
        ]);

        // Add new photos
        if ($request->hasFile('images')) {
            $maxOrder = $milestone->photos()->max('display_order') ?? -1;
            foreach ($request->file('images') as $index => $file) {
                $milestone->photos()->create([
                    'file_path' => $file->store('story', 'public'),
                    'display_order' => $maxOrder + $index + 1,
                ]);
            }
        }

        return back()->with('success', 'Milestone updated.');
    }

    public function destroy(StoryMilestone $milestone)
    {
        foreach ($milestone->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $milestone->delete();
        return back()->with('success', 'Milestone deleted.');
    }

    public function destroyPhoto(StoryPhoto $photo)
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();
        return back()->with('success', 'Photo removed.');
    }
}
