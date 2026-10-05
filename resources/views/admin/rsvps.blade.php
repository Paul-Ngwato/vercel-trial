@extends('layouts.admin', ['pageTitle' => 'RSVP Management'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guests..."
               class="px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
        <select name="attendance" class="px-4 py-2 rounded-lg border border-gray-300 text-sm outline-none">
            <option value="">All Status</option>
            <option value="attending" {{ request('attendance') === 'attending' ? 'selected' : '' }}>Attending</option>
            <option value="not_attending" {{ request('attendance') === 'not_attending' ? 'selected' : '' }}>Not Attending</option>
        </select>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm hover:bg-primary/90">Filter</button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Relationship</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Supporting</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Attendance</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Guests</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $guest->name }}</td>
                        <td class="px-4 py-3 text-gray-600 capitalize">{{ $guest->relationship === 'other' ? $guest->relationship_other : $guest->relationship }}</td>
                        <td class="px-4 py-3 text-gray-600 capitalize">{{ $guest->supporting }}</td>
                        <td class="px-4 py-3">
                            @if($guest->rsvp)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $guest->rsvp->attendance === 'attending' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $guest->rsvp->attendance === 'attending' ? 'Attending' : 'Not Attending' }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $guest->rsvp?->guest_count ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $guest->email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.rsvps.destroy', $guest) }}" method="POST" onsubmit="return confirm('Delete this guest?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No RSVPs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $guests->links() }}</div>
</div>
@endsection
