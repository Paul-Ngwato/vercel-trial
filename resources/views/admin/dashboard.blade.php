@extends('layouts.admin', ['pageTitle' => 'Dashboard'])

@section('content')
{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-primary to-primary-light rounded-2xl p-6 sm:p-8 mb-8 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 blur-2xl"></div>
    <div class="relative">
        <p class="text-white/50 text-sm font-medium mb-1">Welcome back <i class="bi bi-hand-index-thumb-fill"></i></p>
        <h1 class="text-xl sm:text-2xl font-bold">{{ $wedding->full_title ?? 'Wedding Dashboard' }}</h1>
        <p class="text-white/50 text-sm mt-1">{{ $wedding->wedding_date ? \Carbon\Carbon::parse($wedding->wedding_date)->format('F j, Y') : 'Set your wedding date' }}</p>
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-pencil-square"></i> Names, date & venue</a>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-images"></i> Hero & intro photos</a>
            <a href="{{ route('admin.story') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-journal-bookmark-fill"></i> Our Story</a>
            <a href="{{ route('admin.events') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-calendar2-week-fill"></i> Schedule</a>
            <a href="{{ route('admin.information') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-card-list"></i> Good to Know</a>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/15 text-white px-3.5 py-1.5 rounded-full transition-colors"><i class="bi bi-envelope-fill"></i> Contact info</a>
        </div>
    </div>
</div>

{{-- ═══ Quick: what needs your attention ═══ --}}
@if(($stats['photos_pending'] ?? 0) + ($stats['messages_pending'] ?? 0) > 0)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex flex-wrap items-center gap-3">
    <i class="bi bi-bell-fill text-amber-500"></i>
    <p class="text-sm text-amber-700 flex-1 min-w-[200px]">
        <span class="font-semibold">{{ ($stats['photos_pending'] ?? 0) + ($stats['messages_pending'] ?? 0) }}</span>
        item(s) are waiting for your review — photos and well wishes.
    </p>
    <a href="{{ route('admin.notifications') }}" class="text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition-colors">Review now</a>
</div>
@endif

{{-- ═══ Primary Stats ═══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- RSVPs --}}
    <a href="{{ route('admin.rsvps') }}" class="stat-card bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="bi bi-people-fill text-xl text-blue-500"></i>
            </div>
            @if($stats['attending'] > 0)
                <span class="text-[10px] font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full inline-flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> {{ $stats['attending'] }} attending</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_rsvps'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total RSVPs</p>
    </a>

    {{-- Guests --}}
    <a href="{{ route('admin.rsvps') }}" class="stat-card bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i class="bi bi-person-fill text-xl text-purple-500"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_guests'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Guests</p>
    </a>

    {{-- Photos --}}
    <a href="{{ route('admin.photos', ['status' => 'pending']) }}" class="stat-card bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="bi bi-camera-fill text-xl text-amber-500"></i>
            </div>
            @if($stats['photos_pending'] > 0)
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $stats['photos_pending'] }} pending</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['photos_approved'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Guest Photos</p>
    </a>

    {{-- Wishes --}}
    <a href="{{ route('admin.guestbook', ['status' => 'pending']) }}" class="stat-card bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center">
                <i class="bi bi-chat-heart-fill text-xl text-pink-500"></i>
            </div>
            @if($stats['messages_pending'] > 0)
                <span class="text-[10px] font-semibold text-pink-600 bg-pink-50 px-2 py-0.5 rounded-full">{{ $stats['messages_pending'] }} pending</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['messages_approved'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Well Wishes</p>
    </a>
</div>

{{-- Recent Activity --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100/80 flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-800">Recent Activity</h3>
        <a href="{{ route('admin.notifications') }}" class="text-[11px] font-medium text-secondary hover:underline">View all</a>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($recentActivity as $activity)
            <div class="px-6 py-3.5 flex items-center gap-3.5 hover:bg-gray-50/50 transition-colors">
                <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center flex-shrink-0"><i class="bi {{ $activity['icon'] }}"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] text-gray-700">{{ $activity['text'] }}</p>
                </div>
                <span class="text-[11px] text-gray-400 whitespace-nowrap flex-shrink-0">{{ $activity['time'] }}</span>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <div class="text-3xl mb-3 text-gray-300"><i class="bi bi-inbox-fill"></i></div>
                <p class="text-sm text-gray-400">No activity yet</p>
                <p class="text-xs text-gray-300 mt-1">Activity will appear here when guests interact with the site</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
