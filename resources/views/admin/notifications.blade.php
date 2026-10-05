@extends('layouts.admin', ['pageTitle' => 'Notifications'])

@section('content')
<div class="mb-6">
    <p class="text-gray-500 text-sm">All recent activity from guests — RSVPs, photos, and well wishes.</p>
</div>

@if($notifications->isEmpty())
    <div class="bg-white rounded-xl border border-secondary/10 p-12 text-center">
        <div class="text-5xl mb-4 text-gray-300"><i class="bi bi-bell-fill"></i></div>
        <h3 class="text-gray-600 font-medium">No notifications yet</h3>
        <p class="text-gray-400 text-sm mt-1">Activity from your guests will appear here.</p>
    </div>
@else
    <div class="bg-white rounded-xl border border-secondary/10 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50">
            @foreach($notifications as $n)
                <a href="{{ $n['link'] }}" class="flex items-start gap-4 px-6 py-4 hover:bg-ivory/50 transition-colors">
                    <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="bi {{ $n['icon'] }} text-xl"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm">{{ $n['title'] }}</p>
                        <p class="text-gray-500 text-sm mt-0.5">{{ $n['detail'] }}</p>
                    </div>
                    <div class="text-xs text-gray-400 flex-shrink-0 mt-1">
                        {{ $n['time']->diffForHumans() }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
@endsection
