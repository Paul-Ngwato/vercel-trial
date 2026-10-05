<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::query();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $photos = $query->latest()->paginate(20);

        return view('admin.photos', ['photos' => $photos, 'wedding' => $this->getWedding()]);
    }

    public function approve(Photo $photo)
    {
        $photo->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Photo approved.');
    }

    public function reject(Photo $photo)
    {
        $photo->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Photo rejected.');
    }

    public function destroy(Photo $photo)
    {
        $photo->delete();
        return back()->with('success', 'Photo deleted.');
    }
}
