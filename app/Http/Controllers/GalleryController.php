<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\User;
use App\Notifications\NewPhotoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function index()
    {
        $wedding = $this->getWedding();
        $photos = Photo::approved()->latest()->get();
        return view('pages.gallery', compact('wedding', 'photos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uploader_name' => 'required|string|max:255',
            'caption' => 'nullable|string|max:500',
            'photos' => 'required|array|max:10',
            'photos.*' => 'file|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        // Stay on the gallery with the typed details kept
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        foreach ($request->file('photos') as $file) {
            $photo = Photo::create([
                'uploader_name' => $validated['uploader_name'],
                'caption' => $validated['caption'] ?? null,
                'file_path' => $file->store('photos', 'public'),
            ]);

            // Notify admin
            $this->notifyAdmin(new NewPhotoNotification($photo));
        }

        // Stay on the gallery, confirmation shown as an elegant modal
        return back()->with('gallery_success', true);
    }

    protected function notifyAdmin($notification): void
    {
        User::where('role', 'super_admin')->each(fn ($admin) => $admin->notify($notification));
    }
}
