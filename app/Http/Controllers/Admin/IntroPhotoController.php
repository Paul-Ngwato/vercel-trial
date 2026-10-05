<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntroImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IntroPhotoController extends Controller
{
    public function index()
    {
        $wedding = $this->getWedding();
        return view('admin.popup-photos', compact('wedding'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'intro_images' => 'required|array|min:1|max:5',
            'intro_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $wedding = $this->getWedding();
        $existing = IntroImage::where('wedding_id', $wedding->id)->count();
        $remaining = 5 - $existing;
        if ($remaining <= 0) {
            return back()->with('error', 'You already have 5 intro popup photos. Remove one before adding more.');
        }

        $files = array_slice($request->file('intro_images'), 0, $remaining);
        $next = (int) IntroImage::where('wedding_id', $wedding->id)->max('sort_order') + 1;

        $count = 0;
        foreach ($files as $file) {
            IntroImage::create([
                'wedding_id' => $wedding->id,
                'image_path' => $file->store('intro', 'public'),
                'sort_order' => $next++,
            ]);
            $count++;
        }

        return back()->with('success', $count . ' intro popup photo' . ($count === 1 ? '' : 's') . ' added. They now pop out in the tap-to-reveal intro.');
    }

    public function destroy(IntroImage $introImage)
    {
        $wedding = $this->getWedding();
        if ($introImage->wedding_id !== $wedding->id) {
            abort(404);
        }

        Storage::disk('public')->delete($introImage->image_path);
        $introImage->delete();

        return back()->with('success', 'Intro popup photo removed.');
    }
}