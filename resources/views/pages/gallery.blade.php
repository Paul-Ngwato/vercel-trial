@extends('layouts.public')

@section('content')
{{-- Floating back-to-invitation button (mobile, above the bottom nav) --}}
<a href="{{ route('home') . '#top' }}"
   class="md:hidden fixed bottom-[76px] right-4 z-40 w-12 h-12 rounded-full bg-primary/95 text-secondary border border-secondary/40 shadow-lg shadow-black/30 flex items-center justify-center backdrop-blur transition-all duration-200 hover:scale-105 active:scale-95"
   title="Back to invitation" aria-label="Back to the invitation home page">
    <i class="bi bi-arrow-left text-xl"></i>
</a>

<section class="pt-24 md:pt-32 pb-16 bg-ivory">
    @if(session('gallery_success'))
        <div id="gallery-flash" class="fixed inset-0 z-[9990] bg-primary-darker/60 backdrop-blur-sm flex items-center justify-center p-6" style="background: rgba(13,31,21,.55);">
            <div class="bg-[#fffdf6] rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl" style="border:1px solid rgba(201,168,76,.35); animation: galleryPop .45s cubic-bezier(.2,.9,.3,1.2);">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-secondary/20 text-secondary flex items-center justify-center text-3xl"><i class="bi bi-camera-fill"></i></div>
                <h3 class="font-playfair text-2xl text-primary">Thank you!</h3>
                <p class="text-gray-500 mt-2 text-sm">Your photos were received — they'll appear in the album once the couple approves them.</p>
                <button type="button" onclick="this.closest('#gallery-flash').remove()" class="mt-5 bg-primary text-white text-sm font-semibold px-8 py-2.5 rounded-full hover:bg-primary-light transition-colors">Lovely!</button>
            </div>
        </div>
        <style>@keyframes galleryPop { from { transform: scale(.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }</style>
    @endif
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-10">
            <p class="text-secondary/80 tracking-[0.3em] uppercase text-sm mb-4">Share Your Memories</p>
            <h1 class="font-playfair text-4xl md:text-5xl text-primary">Photo Gallery</h1>
            <div class="w-20 h-0.5 bg-secondary mx-auto mt-6"></div>
            <p class="text-gray-600 mt-4 max-w-xl mx-auto">Your photos are part of our story. Share a special moment from our journey or wedding celebration.</p>
        </div>

        {{-- Photo Grid --}}
        @if($photos->isNotEmpty())
            <div class="columns-2 md:columns-3 lg:columns-4 gap-4 mb-12">
                @foreach($photos as $photo)
                    <div class="break-inside-avoid mb-4 group cursor-pointer"
                         x-data="{ show: false, fs: false, toggleFs() { const el = this.$refs.lightbox; if (!document.fullscreenElement) { (el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen).call(el); } else { document.exitFullscreen(); } } }"
                         @click="show = true">
                        <img src="{{ asset('storage/' . $photo->file_path) }}"
                             alt="{{ $photo->caption ?? 'Wedding photo' }}"
                             class="rounded-xl shadow-sm w-full group-hover:shadow-md transition-shadow duration-300"
                             loading="lazy"
                             draggable="false">
                        @if($photo->caption)
                            <p class="text-gray-500 text-sm mt-2">{{ $photo->caption }}</p>
                        @endif
                        <p class="text-gray-400 text-xs mt-1 flex items-center gap-1"><i class="bi bi-camera"></i> {{ $photo->uploader_name }}</p>

                        {{-- Lightbox --}}
                        <div x-show="show" x-cloak
                             x-transition.opacity.duration.300ms
                             class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4 sm:p-6"
                             x-ref="lightbox"
                             @click.self="show = false"
                             @keydown.escape.window="show = false"
                             @fullscreenchange="fs = document.fullscreenElement === $refs.lightbox">

                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                 alt="{{ $photo->caption ?? 'Wedding photo' }}"
                                 class="max-w-full max-h-full object-contain rounded-xl shadow-2xl select-none"
                                 draggable="false"
                                 @click.stop>

                            {{-- Caption pill --}}
                            @if($photo->caption || $photo->uploader_name)
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-max max-w-[92%] px-5 py-2 rounded-full bg-black/60 backdrop-blur text-white/90 text-xs sm:text-sm truncate" @click.stop>
                                    @if($photo->caption)<span class="font-medium">{{ $photo->caption }}</span>@endif
                                    @if($photo->caption && $photo->uploader_name)<span class="text-white/50"> · </span>@endif
                                    @if($photo->uploader_name)<span class="text-white/60">by {{ $photo->uploader_name }}</span>@endif
                                </div>
                            @endif

                            {{-- Top action bar: download · fullscreen · close --}}
                            <div class="absolute top-3 sm:top-5 right-3 sm:right-5 flex items-center gap-2">
                                <a href="{{ asset('storage/' . $photo->file_path) }}"
                                   download
                                   title="Download photo"
                                   class="w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 backdrop-blur text-white flex items-center justify-center transition-all duration-200 active:scale-90"
                                   @click.stop>
                                    <i class="bi bi-download text-lg"></i>
                                </a>
                                <button type="button"
                                        @click.stop="toggleFs()"
                                        title="Toggle fullscreen"
                                        class="w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 backdrop-blur text-white flex items-center justify-center transition-all duration-200 active:scale-90">
                                    <i class="bi text-lg" :class="fs ? 'bi-fullscreen-exit' : 'bi-fullscreen'"></i>
                                </button>
                                <button type="button"
                                        @click.stop="show = false"
                                        title="Close (Esc)"
                                        class="w-11 h-11 rounded-full bg-white/10 hover:bg-red-500/90 backdrop-blur text-white flex items-center justify-center transition-all duration-200 active:scale-90">
                                    <i class="bi bi-x-lg text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-400">
                <div class="text-5xl mb-4 text-gray-300"><i class="bi bi-camera-fill"></i></div>
                <p>No photos yet. Be the first to share a memory!</p>
            </div>
        @endif

        {{-- Upload Form --}}
        <div id="upload" class="bg-white rounded-2xl shadow-sm border border-secondary/10 p-6 md:p-8 max-w-2xl mx-auto" style="scroll-margin-top: 90px;">
            <h2 class="font-playfair text-2xl text-primary text-center mb-6">Upload Your Photos</h2>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm mb-5">
                    <p class="font-semibold mb-1"><i class="bi bi-exclamation-circle-fill mr-1"></i>Oops — please check:</p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $er)<li>{{ $er }}</li>@endforeach
                    </ul>
                </div>
                <script>document.getElementById('upload')?.scrollIntoView({ behavior: 'smooth', block: 'center' });</script>
            @endif

            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                    <input type="text" name="uploader_name" value="{{ old('uploader_name') }}" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition"
                           placeholder="Enter your name">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption <span class="text-gray-400">(Optional)</span></label>
                    <input type="text" name="caption" value="{{ old('caption') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition"
                           placeholder="Add a caption...">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photos * (up to 10)</label>
                    <input type="file" name="photos[]" multiple accept="image/*"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, or WebP — Max 10MB each</p>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3 rounded-full text-sm uppercase tracking-wider transition-all duration-300 inline-flex items-center justify-center gap-2">
                    Upload Photos <i class="bi bi-camera-fill"></i>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
