@extends('layouts.admin', ['pageTitle' => 'Popup Photos'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
@endif
{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Intro Popup Photos</h1>
        <p class="text-sm text-gray-400 mt-1">These photos flash across the screen, one after another, after a guest taps to reveal on the homepage — then the site opens.</p>
    </div>
    <a href="{{ route('home') }}" target="_blank"
       class="hidden sm:inline-flex items-center gap-2 text-xs font-medium text-secondary bg-secondary/10 hover:bg-secondary/20 px-4 py-2 rounded-lg transition-colors">
        <i class="bi bi-eye"></i> Preview Intro
    </a>
</div>

{{-- Upload card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 p-6 mb-6">
    <div class="flex items-center gap-2 mb-1">
        <i class="bi bi-cloud-arrow-up text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Add Popup Photos</h3>
        <span class="ml-auto text-[11px] text-gray-400 hidden sm:inline">{{ $wedding->introImages->count() }} / 5 used</span>
    </div>
    <p class="text-xs text-gray-400 mb-4">Max 5 photos, 5MB each. These are the photos that pop out around the screen during the intro reveal.</p>

    @if($wedding->introImages->count() < 5)
        <form action="{{ route('admin.popup-photos.store') }}" method="POST" enctype="multipart/form-data">
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
        <p class="text-xs text-amber-600">Maximum of 5 popup photos reached — remove one to add more.</p>
    @endif
</div>

{{-- Existing photos --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 p-6">
    <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-images text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Your Popup Photos</h3>
    </div>

    @if($wedding->introImages->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($wedding->introImages as $i => $img)
                <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-[4/5] bg-gray-100 group">
                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Popup photo {{ $i + 1 }}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1.5 left-1.5 text-[9px] font-medium text-white/90 bg-black/50 px-1.5 py-0.5 rounded">{{ $i + 1 }}</span>
                    <button type="button" onclick="adminCropOpen('{{ asset('storage/' . $img->image_path) }}?v={{ $img->updated_at?->timestamp }}', 'cropFormIntro{{ $img->id }}')" class="absolute top-1.5 left-1.5 w-7 h-7 rounded-full bg-[#c9a84c] text-primary flex items-center justify-center shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" title="Adjust how guests see it"><i class="bi bi-scissors text-xs"></i></button>
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
        <div class="text-center py-12">
            <div class="text-3xl mb-3 text-gray-300"><i class="bi bi-camera-reels"></i></div>
            <p class="text-sm text-gray-400">No popup photos yet.</p>
            <p class="text-xs text-gray-300 mt-1">Add up to 5 above — they'll flash on screen one by one after the tap-to-reveal on the homepage.</p>
        </div>
    @endif
</div>

{{-- Hidden re-crop forms (one per popup photo, kept outside every other form) --}}
<div class="hidden" aria-hidden="true">
    @foreach($wedding->introImages as $img)
        <form id="cropFormIntro{{ $img->id }}" action="{{ route('admin.photos.crop', ['type' => 'intro', 'id' => $img->id]) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="x" value="0"><input type="hidden" name="y" value="0">
            <input type="hidden" name="width" value="0"><input type="hidden" name="height" value="0">
        </form>
    @endforeach
</div>
@endsection