@extends('layouts.admin', ['pageTitle' => 'Photo Moderation'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
@endif

<div class="flex gap-3 mb-6">
    <a href="{{ route('admin.photos', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status', 'pending') === 'pending' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Pending</a>
    <a href="{{ route('admin.photos', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Approved</a>
    <a href="{{ route('admin.photos', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'rejected' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Rejected</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @forelse($photos as $photo)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="relative">
                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="" class="w-full h-40 object-cover" style="object-position: 50% 25%;">
                <button type="button" onclick="adminCropOpen('{{ asset('storage/' . $photo->file_path) }}?v={{ $photo->updated_at?->timestamp }}', 'cropFormGallery{{ $photo->id }}')" class="absolute top-1.5 left-1.5 w-7 h-7 rounded-full bg-[#c9a84c] text-primary flex items-center justify-center shadow" title="Adjust how guests see it"><i class="bi bi-scissors text-xs"></i></button>
            </div>
            <div class="p-3">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $photo->uploader_name }}</p>
                @if($photo->caption)
                    <p class="text-xs text-gray-500 truncate">{{ $photo->caption }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">{{ $photo->created_at->diffForHumans() }}</p>
                <div class="flex gap-2 mt-2">
                    @if($photo->status === 'pending')
                        <form action="{{ route('admin.photos.approve', $photo) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-green-100 text-green-700 text-xs py-1.5 rounded-lg hover:bg-green-200">Approve</button>
                        </form>
                        <form action="{{ route('admin.photos.reject', $photo) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-red-100 text-red-700 text-xs py-1.5 rounded-lg hover:bg-red-200">Reject</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-500 text-xs px-2"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12 text-gray-400">No photos found.</div>
    @endforelse
</div>

{{-- Hidden re-crop forms (one per guest photo, kept outside every other form) --}}
<div class="hidden" aria-hidden="true">
    @foreach($photos as $photo)
        <form id="cropFormGallery{{ $photo->id }}" action="{{ route('admin.photos.crop', ['type' => 'gallery', 'id' => $photo->id]) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="x" value="0"><input type="hidden" name="y" value="0">
            <input type="hidden" name="width" value="0"><input type="hidden" name="height" value="0">
        </form>
    @endforeach
</div>
<div class="mt-6">{{ $photos->links() }}</div>
@endsection
