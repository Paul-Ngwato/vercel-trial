<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Models\HeroImage;
use App\Models\Wedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $wedding = $this->getWedding();
        return view('admin.settings', compact('wedding'));
    }

    public function update(Request $request)
    {
        $wedding = $this->getWedding();

        $validated = $request->validate([
            'bride_name' => 'required|string|max:255',
            'groom_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'wedding_time' => 'nullable',
            'ceremony_venue' => 'nullable|string|max:255',
            'ceremony_address' => 'nullable|string',
            'ceremony_map_url' => 'nullable|url',
            'reception_venue' => 'nullable|string|max:255',
            'reception_address' => 'nullable|string',
            'reception_map_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'couple_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'invitation_message' => 'nullable|string|max:1000',
            'dress_code' => 'nullable|string|max:255',
            'rsvp_deadline' => 'nullable|string|max:255',
            'family_bride_info' => 'nullable|string|max:500',
            'family_groom_info' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('couple_photo')) {
            $validated['couple_photo_path'] = $request->file('couple_photo')->store('couple', 'public');
        }

        if ($request->hasFile('logo')) {
            if ($wedding->logo_path) {
                Storage::disk('public')->delete($wedding->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['couple_photo'], $validated['logo']);
        $wedding->update($validated);

        return back()->with('success', 'Settings updated.');
    }

    /**
     * Church & pastor details — shown in the ceremony section.
     * Creates the church record the first time it is saved.
     */
    public function updateChurch(Request $request)
    {
        $wedding = $this->getWedding();

        $validated = $request->validate([
            'church_name' => 'required|string|max:255',
            'pastor_name' => 'nullable|string|max:255',
            'church_message' => 'nullable|string|max:1000',
        ]);

        $church = $wedding->church ?? new Church(['wedding_id' => $wedding->id]);

        $church->name = $validated['church_name'];
        $church->pastor_name = $validated['pastor_name'] ?? null;
        $church->message_from_church = $validated['church_message'] ?? null;
        $church->wedding_id = $wedding->id;
        $church->save();

        return back()->with('success', 'Church details updated.');
    }

    public function storeHero(Request $request)
    {
        $request->validate([
            'hero_images' => 'required|array|min:1|max:12',
            'hero_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $wedding = $this->getWedding();
        $next = (int) HeroImage::where('wedding_id', $wedding->id)->max('sort_order') + 1;

        $count = 0;
        foreach ($request->file('hero_images') as $file) {
            HeroImage::create([
                'wedding_id' => $wedding->id,
                'image_path' => $file->store('hero', 'public'),
                'sort_order' => $next++,
            ]);
            $count++;
        }

        return back()->with('success', $count . ' hero photo' . ($count === 1 ? '' : 's') . ' added. The homepage slideshow has been updated.');
    }

    public function destroyHero(HeroImage $heroImage)
    {
        $wedding = $this->getWedding();
        if ($heroImage->wedding_id !== $wedding->id) {
            abort(404);
        }

        Storage::disk('public')->delete($heroImage->image_path);
        $heroImage->delete();

        return back()->with('success', 'Hero photo removed.');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->onlyInput('name', 'email');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (! empty($validated['password'])) {
            $user->password = $validated['password']; // hashed by the model's cast
        }
        $user->save();

        return back()->with('success', 'Your account has been updated.');
    }
}
