<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RsvpController as AdminRsvpController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\GuestbookController as AdminGuestbookController;
use App\Http\Controllers\Admin\ImageCropController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\InformationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\IntroPhotoController;

// ── Uploaded photos in object storage (PUBLIC_DISK=s3) ──
// Templates link to /storage/...; with no public/storage symlink (e.g. on Vercel)
// those requests are sent on to the bucket.
if (config('filesystems.disks.public.driver') === 's3') {
    Route::get('/storage/{path}', fn (string $path) => redirect()->away(
        Storage::disk('public')->url($path),
        302,
        ['Cache-Control' => 'public, max-age=86400'],
    ))->where('path', '.*');
}

// ── Public: one-page site ──
Route::get('/', HomeController::class)->name('home');

// Legacy URLs now land on the matching section of the homepage
Route::get('/our-story', [PageController::class, 'story'])->name('story');
Route::get('/invitation', [PageController::class, 'invitation'])->name('invitation');
Route::get('/schedule', [PageController::class, 'schedule'])->name('schedule');
Route::get('/location', [PageController::class, 'location'])->name('location');
Route::get('/information', [PageController::class, 'information'])->name('information');

// ── RSVP (submits into the homepage section) ──
Route::get('/rsvp', [RsvpController::class, 'index'])->name('rsvp');
Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');

// ── Guestbook (submits into the homepage section) ──
Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook');
Route::post('/guestbook', [GuestbookController::class, 'store'])->name('guestbook.store');

// ── Gallery: the only separate page (photo album) ──
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');

// ── Admin ──
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/rsvps', [AdminRsvpController::class, 'index'])->name('rsvps');
        Route::delete('/rsvps/{guest}', [AdminRsvpController::class, 'destroy'])->name('rsvps.destroy');

        Route::get('/photos', [PhotoController::class, 'index'])->name('photos');
        Route::post('/photos/{photo}/approve', [PhotoController::class, 'approve'])->name('photos.approve');
        Route::post('/photos/{photo}/reject', [PhotoController::class, 'reject'])->name('photos.reject');
        Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

        Route::get('/guestbook', [AdminGuestbookController::class, 'index'])->name('guestbook');
        Route::post('/guestbook/{message}/approve', [AdminGuestbookController::class, 'approve'])->name('guestbook.approve');
        Route::post('/guestbook/{message}/reject', [AdminGuestbookController::class, 'reject'])->name('guestbook.reject');
        Route::delete('/guestbook/{message}', [AdminGuestbookController::class, 'destroy'])->name('guestbook.destroy');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        Route::get('/information', [InformationController::class, 'index'])->name('information');
        Route::post('/information', [InformationController::class, 'store'])->name('information.store');
        Route::put('/information/{item}', [InformationController::class, 'update'])->name('information.update');
        Route::delete('/information/{item}', [InformationController::class, 'destroy'])->name('information.destroy');
        Route::patch('/information/{item}/toggle', [InformationController::class, 'togglePublished'])->name('information.toggle');

        Route::get('/story', [StoryController::class, 'index'])->name('story');
        Route::post('/story', [StoryController::class, 'store'])->name('story.store');
        Route::put('/story/{milestone}', [StoryController::class, 'update'])->name('story.update');
        Route::delete('/story/{milestone}', [StoryController::class, 'destroy'])->name('story.destroy');
        Route::delete('/story/photo/{photo}', [StoryController::class, 'destroyPhoto'])->name('story.photo.destroy');

        Route::get('/events', [EventController::class, 'index'])->name('events');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account');
        Route::put('/settings/church', [SettingsController::class, 'updateChurch'])->name('settings.church');
        Route::post('/settings/hero', [SettingsController::class, 'storeHero'])->name('settings.hero.store');
        Route::delete('/settings/hero/{heroImage}', [SettingsController::class, 'destroyHero'])->name('settings.hero.destroy');

        // Re-crop any already-uploaded photo so the admin can fine-tune how it looks to guests
        Route::put('/photos/{type}/{id}/crop', [ImageCropController::class, 'update'])->name('photos.crop');

        Route::get('/popup-photos', [IntroPhotoController::class, 'index'])->name('popup-photos');
        Route::post('/popup-photos', [IntroPhotoController::class, 'store'])->name('popup-photos.store');
        Route::delete('/popup-photos/{introImage}', [IntroPhotoController::class, 'destroy'])->name('popup-photos.destroy');
    });
});
