<?php

namespace App\Http\Controllers;

use App\Models\GuestbookMessage;
use App\Models\User;
use App\Notifications\NewGuestbookMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestbookController extends Controller
{
    public function index()
    {
        return redirect()->to(route('home') . '#guestbook');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Land back INSIDE the wishes section with the typed message kept
        if ($validator->fails()) {
            return redirect()->to(route('home') . '#guestbook')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $msg = GuestbookMessage::create($validated);

        // Notify admin
        $this->notifyAdmin(new NewGuestbookMessageNotification($msg));

        // Back to the wishes section with a success flag (shown as a classy modal)
        return redirect()
            ->to(route('home') . '#guestbook')
            ->with('wish_success', $msg->name);
    }

    protected function notifyAdmin($notification): void
    {
        User::where('role', 'super_admin')->each(fn ($admin) => $admin->notify($notification));
    }
}
