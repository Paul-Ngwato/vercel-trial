@extends('layouts.admin', ['pageTitle' => 'Schedule Management'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Current Events --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Current Schedule</h3>
        <div class="space-y-3">
            @forelse($events as $event)
                <div class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0">
                    <div class="w-16 text-right text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</div>
                    <div class="w-1 h-8 bg-secondary rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                        @if($event->description)
                            <p class="text-xs text-gray-500">{{ $event->description }}</p>
                        @endif
                    </div>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-500 text-xs"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">No events yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Add Event Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Add Event</h3>
        <form action="{{ route('admin.events.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                        <input type="time" name="start_time" required class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="{{ $events->count() + 1 }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg text-sm font-medium hover:bg-primary/90">Add Event</button>
            </div>
        </form>
    </div>
</div>
@endsection
