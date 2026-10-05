<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestbookMessage;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function index(Request $request)
    {
        $query = GuestbookMessage::query();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $messages = $query->latest()->paginate(20);

        return view('admin.guestbook', ['messages' => $messages, 'wedding' => $this->getWedding()]);
    }

    public function approve(GuestbookMessage $message)
    {
        $message->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Message approved.');
    }

    public function reject(GuestbookMessage $message)
    {
        $message->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Message rejected.');
    }

    public function destroy(GuestbookMessage $message)
    {
        $message->delete();
        return back()->with('success', 'Message deleted.');
    }
}
