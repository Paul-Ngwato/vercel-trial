@extends('layouts.admin', ['pageTitle' => 'Settings'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm border border-red-100">
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ═══ Your Account (login details) ═══ --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-2 mb-1">
        <i class="bi bi-person-badge text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Your Account</h3>
    </div>
    <p class="text-xs text-gray-400 mb-4">Update the name, email, or password you use to sign in to this admin.</p>

    <form action="{{ route('admin.settings.account') }}" method="POST">
        @csrf @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-xs text-gray-400 font-normal">(optional — min 8 characters)</span></label>
                <input type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
            </div>
            <div class="md:col-span-2 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                    <input type="password" name="current_password" required autocomplete="current-password" placeholder="Confirm with your current password"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    <i class="bi bi-check2-circle"></i> Update Account
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ═══ Hero Slideshow ═══ --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-2 mb-1">
        <i class="bi bi-images text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Hero Slideshow</h3>
        <span class="ml-auto text-[11px] text-gray-400 hidden sm:inline">Shown behind the names on the homepage</span>
    </div>
    <p class="text-xs text-gray-400 mb-4">Add several photos — the homepage slowly fades between them. The first photo is used in the opening scratch reveal. Recommended: wide landscape shots (1200x800px+), max 5MB each.</p>

    {{-- Upload --}}
    <form action="{{ route('admin.settings.hero.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
        @csrf
        <div class="flex flex-wrap items-center gap-3">
            <input type="file" name="hero_images[]" multiple accept="image/*" required
                   class="flex-1 min-w-[220px] px-3 py-2 rounded-lg border border-gray-300 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <i class="bi bi-plus-lg"></i> Add Photos
            </button>
        </div>
        @error('hero_images')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
        @error('hero_images.*')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
    </form>

    {{-- Slides grid --}}
    @if($wedding->heroImages->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($wedding->heroImages as $i => $img)
                <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-[4/3] bg-gray-100 group">
                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Hero photo {{ $i + 1 }}" class="w-full h-full object-cover" id="heroImg{{ $img->id }}">
                    @if($i === 0)
                        <span class="absolute top-1.5 left-1.5 bg-primary/85 text-white text-[9px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded">First</span>
                    @endif
                    <span class="absolute bottom-1.5 left-1.5 text-[9px] font-medium text-white/90 bg-black/50 px-1.5 py-0.5 rounded">{{ $i + 1 }}</span>
                    <div class="absolute top-1.5 left-1.5 {{ $i === 0 ? 'top-8' : '' }} flex gap-1">
                        <button type="button" onclick="adminCropOpen(document.getElementById('heroImg{{ $img->id }}').src, 'cropFormHero{{ $img->id }}')" class="w-7 h-7 rounded-full bg-[#c9a84c] text-primary flex items-center justify-center shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" title="Adjust how guests see it"><i class="bi bi-scissors text-xs"></i></button>
                    </div>
                    <form action="{{ route('admin.settings.hero.destroy', $img) }}" method="POST" class="absolute top-1.5 right-1.5">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove this hero photo?')"
                                class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity"
                                title="Remove photo">
                            <i class="bi bi-trash3 text-xs"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-400">No hero photos yet — add a few above. If you previously uploaded a single hero photo it is still shown until you add slides.</p>
    @endif
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Couple Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Couple Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bride Name *</label>
                    <input type="text" name="bride_name" value="{{ $wedding->bride_name }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Groom Name *</label>
                    <input type="text" name="groom_name" value="{{ $wedding->groom_name }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
            </div>
        </div>

        {{-- Wedding Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Wedding Details</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" name="wedding_date" value="{{ $wedding->wedding_date?->format('Y-m-d') }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <input type="time" name="wedding_time" value="{{ $wedding->wedding_time }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                </div>
            </div>
        </div>

        {{-- Invitation --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="bi bi-envelope-heart mr-1.5 text-gray-500"></i>Invitation</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invitation Message</label>
                    <textarea name="invitation_message" rows="3" placeholder="We warmly invite you to share in our joy..." class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->invitation_message }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couple Photo <span class="text-gray-400">(on the invitation card)</span></label>
                        @if($wedding->couple_photo_path)
                            <div class="mb-2"><img src="{{ asset('storage/' . $wedding->couple_photo_path) }}" class="h-24 rounded-lg object-cover border border-gray-200" alt=""></div>
                        @endif
                        <input type="file" name="couple_photo" accept="image/*" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wedding Logo <span class="text-gray-400">(top of the invitation card)</span></label>
                        @if($wedding->logo_path)
                            <div class="mb-2"><img src="{{ asset('storage/' . $wedding->logo_path) }}" class="h-24 rounded-lg object-contain border border-gray-200 bg-white p-1" alt=""></div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                        <p class="text-xs text-gray-400 mt-1">PNG with transparent background looks best. Leave blank to skip.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dress Code</label>
                        <input type="text" name="dress_code" value="{{ $wedding->dress_code }}" placeholder="e.g., Formal / Black Tie" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RSVP Deadline</label>
                        <input type="date" name="rsvp_deadline" value="{{ $wedding->rsvp_deadline }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bride's Family Info</label>
                    <textarea name="family_bride_info" rows="2" placeholder="Hosted by Mr. & Mrs. Ochieng..." class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->family_bride_info }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Groom's Family Info</label>
                    <textarea name="family_groom_info" rows="2" placeholder="Together with Mr. & Mrs. Kamau..." class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->family_groom_info }}</textarea>
                </div>
            </div>
        </div>

        {{-- Ceremony --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Ceremony</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                    <input type="text" name="ceremony_venue" value="{{ $wedding->ceremony_venue }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="ceremony_address" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->ceremony_address }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps URL</label>
                    <input type="url" name="ceremony_map_url" value="{{ $wedding->ceremony_map_url }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
            </div>
        </div>

        {{-- Reception --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Reception</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                    <input type="text" name="reception_venue" value="{{ $wedding->reception_venue }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="reception_address" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->reception_address }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps URL</label>
                    <input type="url" name="reception_map_url" value="{{ $wedding->reception_map_url }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
            </div>
        </div>

        {{-- Meta / SEO --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Website Meta</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                    <input type="text" name="meta_title" value="{{ $wedding->meta_title }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ $wedding->meta_description }}</textarea>
                </div>
            </div>
        </div>

        {{-- Colors --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Theme Colors</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary</label>
                    <div class="flex gap-2">
                        <input type="color" name="primary_color" value="{{ $wedding->primary_color }}" class="w-10 h-10 rounded cursor-pointer">
                        <input type="text" value="{{ $wedding->primary_color }}" class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secondary</label>
                    <div class="flex gap-2">
                        <input type="color" name="secondary_color" value="{{ $wedding->secondary_color }}" class="w-10 h-10 rounded cursor-pointer">
                        <input type="text" value="{{ $wedding->secondary_color }}" class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm" readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Contact Info</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="contact_email" value="{{ $wedding->contact_email }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="contact_phone" value="{{ $wedding->contact_phone }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg text-sm font-medium hover:bg-primary/90">Save Settings</button>
    </div>
</form>

{{-- Hidden re-crop forms for hero photos --}}
<div class="hidden" aria-hidden="true">
    @foreach($wedding->heroImages as $img)
        <form id="cropFormHero{{ $img->id }}" action="{{ route('admin.photos.crop', ['type' => 'hero', 'id' => $img->id]) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="x" value="0"><input type="hidden" name="y" value="0">
            <input type="hidden" name="width" value="0"><input type="hidden" name="height" value="0">
        </form>
    @endforeach
</div>

{{-- ═══ Church & Pastor — its own form so it saves independently ═══ --}}
<form action="{{ route('admin.settings.church') }}" method="POST" class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    @csrf @method('PUT')
    <div class="flex items-center gap-2 mb-1">
        <i class="bi bi-church text-gray-500"></i>
        <h3 class="font-semibold text-gray-800">Church & Pastor</h3>
    </div>
    <p class="text-xs text-gray-400 mb-4">Shown in the ceremony section of the website. Leave blank if not applicable.</p>

    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Church Name</label>
            <input type="text" name="church_name" value="{{ old('church_name', $wedding->church->name ?? '') }}" placeholder="e.g., All Saints Cathedral"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pastor / Officiant Name</label>
            <input type="text" name="pastor_name" value="{{ old('pastor_name', $wedding->church->pastor_name ?? '') }}" placeholder="e.g., Rev. Dr. James Mwangi"
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Message from the Church</label>
            <textarea name="church_message" rows="3" placeholder="A blessing or word from the church..."
                      class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-green-700 outline-none resize-none">{{ old('church_message', $wedding->church->message_from_church ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="inline-flex items-center gap-2 bg-secondary text-primary px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-secondary-light transition-colors">
            <i class="bi bi-check2-circle"></i> Save Church Details
        </button>
    </div>
</form>

{{-- M-Pesa is accepted as a manual payment method (no Daraja API integration) --}}
@endsection
