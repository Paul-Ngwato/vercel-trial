@extends('layouts.admin', ['pageTitle' => 'Well Wishes Moderation'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif

<div class="flex gap-3 mb-6">
    <a href="{{ route('admin.guestbook', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status', 'pending') === 'pending' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Pending</a>
    <a href="{{ route('admin.guestbook', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Approved</a>
    <a href="{{ route('admin.guestbook', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'rejected' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Rejected</a>
</div>

<div class="space-y-4">
    @forelse($messages as $msg)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center text-secondary text-sm font-bold">
                            {{ strtoupper(substr($msg->name, 0, 1)) }}
                        </div>
                        <span class="font-medium text-gray-800">{{ $msg->name }}</span>
                        <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-600 mt-2 ml-10">{{ $msg->message }}</p>
                </div>
                <div class="flex gap-2 ml-4">
                    @if($msg->status === 'pending')
                        <form action="{{ route('admin.guestbook.approve', $msg) }}" method="POST">
                            @csrf
                            <button class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg hover:bg-green-200">Approve</button>
                        </form>
                        <form action="{{ route('admin.guestbook.reject', $msg) }}" method="POST">
                            @csrf
                            <button class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg hover:bg-red-200">Reject</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.guestbook.destroy', $msg) }}" method="POST" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-500 text-xs px-2 py-1.5"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 text-gray-400">No well wishes found.</div>
    @endforelse
</div>
<div class="mt-6">{{ $messages->links() }}</div>
@endsection
