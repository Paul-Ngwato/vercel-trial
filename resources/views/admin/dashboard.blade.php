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

{{-- ═══ Intro Popup Photos ═══ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 p-6 mb-6">
    <div class="flex items-center gap-2 mb-1">
        <i class="bi bi-camera-fill text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Intro Popup Photos</h3>
        <span class="ml-auto text-[11px] text-gray-400 hidden sm:inline">Shown in the tap-to-reveal intro on the homepage</span>
    </div>
    <p class="text-xs text-gray-400 mb-4">These photos pop out on the screen, one after another, when a guest taps to reveal on the homepage. Max 5 photos, 5MB each.</p>

    @if($wedding->introImages->count() < 5)
        <form action="{{ route('admin.popup-photos.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
            @csrf
            <div class="flex flex-wrap items-center gap-3">
                <input type="file" name="intro_images[]" multiple accept="image/*" required
                       class="flex-1 min-w-[220px] px-3 py-2 rounded-lg border border-gray-300 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    <i class="bi bi-plus-lg"></i> Add Photos
                </button>
            </div>
            @error('intro_images')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
            @error('intro_images.*')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </form>
    @else
        <p class="text-xs text-amber-600 mb-5">Maximum of 5 popup photos reached — remove one to add more.</p>
    @endif

    @if($wedding->introImages->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($wedding->introImages as $i => $img)
                <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-[4/5] bg-gray-100 group">
                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Intro popup photo {{ $i + 1 }}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1.5 left-1.5 text-[9px] font-medium text-white/90 bg-black/50 px-1.5 py-0.5 rounded">{{ $i + 1 }}</span>
                    <form action="{{ route('admin.popup-photos.destroy', $img) }}" method="POST" class="absolute top-1.5 right-1.5">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove this popup photo?')"
                                class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity"
                                title="Remove photo">
                            <i class="bi bi-trash3 text-xs"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-400">No intro popup photos yet — add up to 5 above. They'll pop out on screen one by one when a guest taps the photo on the homepage intro.</p>
    @endif
</div>

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
