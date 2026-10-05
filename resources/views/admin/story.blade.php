@extends('layouts.admin', ['pageTitle' => 'Our Story'])

@push('styles')
<style>
    .photo-thumb { position: relative; display: inline-block; }
    .photo-thumb img { height: 80px; width: 100px; object-fit: cover; object-position: 50% 22%; border-radius: 8px; border: 1px solid #e5e7eb; }
    .photo-thumb .actions { position: absolute; top: -4px; right: -4px; display: none; gap: 2px; }
    .photo-thumb:hover .actions { display: flex; }
    .photo-thumb .btn-del { width: 20px; height: 20px; border-radius: 50%; background: #ef4444; color: white; border: none; font-size: 10px; cursor: pointer; line-height: 20px; text-align: center; }
    .photo-thumb .btn-crop { width: 20px; height: 20px; border-radius: 50%; background: #c9a84c; color: #1a3c2a; border: none; font-size: 10px; cursor: pointer; line-height: 20px; text-align: center; }
    .crop-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 999; display: flex; align-items: center; justify-content: center; }
    .crop-box { background: white; border-radius: 16px; padding: 24px; max-width: 700px; width: 90%; }
    .crop-preview { max-height: 350px; overflow: hidden; background: #f3f4f6; border-radius: 12px; }
    .crop-preview img { display: block; max-width: 100%; }
    .ratio-btn { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; }
    .ratio-btn.active { background: #1a3c2a; color: white; }
    .ratio-btn:not(.active) { background: #f3f4f6; color: #6b7280; }
</style>
@endpush

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<div class="grid lg:grid-cols-2 gap-8">
    {{-- LEFT: Existing Milestones --}}
    <div>
        <h3 class="font-semibold text-primary mb-4"><i class="bi bi-journal-bookmark-fill mr-1.5 text-secondary"></i>Story Milestones ({{ $milestones->count() }})</h3>

        @forelse($milestones as $m)
            <div class="bg-white rounded-xl border border-secondary/10 p-5 mb-4 shadow-sm">
                {{-- VIEW MODE --}}
                <div id="view-{{ $m->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-secondary/20 text-secondary text-xs font-bold flex items-center justify-center">{{ $m->display_order }}</span>
                                <h4 class="font-semibold text-primary">{{ $m->title }}</h4>
                            </div>
                            <p class="text-gray-600 text-sm mt-2 ml-9">{{ Str::limit($m->content, 180) }}</p>
                            @if($m->photos->count() > 0)
                                <div class="flex gap-2 mt-3 ml-9 overflow-x-auto pb-1">
                                    @foreach($m->photos as $photo)
                                        <div class="photo-thumb">
                                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="">
                                            <div class="actions">
                                                <button type="button" class="btn-crop" title="Adjust how guests see it" onclick="adminCropOpen('{{ asset('storage/' . $photo->file_path) }}?v={{ $photo->updated_at?->timestamp }}', 'cropForm{{ $photo->id }}')">✂</button>
                                                <button type="button" class="btn-del" title="Remove" onclick="removePhoto({{ $photo->id }}, 'Remove this photo?')">✕</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-1 ml-2 flex-shrink-0">
                            <button onclick="showEdit({{ $m->id }})" class="text-secondary hover:text-secondary/80 text-xs px-2 py-1 rounded hover:bg-secondary/10"><i class="bi bi-pencil-square"></i> Edit</button>
                            <form action="{{ route('admin.story.destroy', $m) }}" method="POST" onsubmit="return confirm('Delete this entire milestone?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded hover:bg-red-50"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- EDIT MODE (hidden by default) --}}
                <div id="edit-{{ $m->id }}" style="display:none;">
                    <form action="{{ route('admin.story.update', $m) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="space-y-3">
                            <div class="grid grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Order</label>
                                    <input type="number" name="display_order" value="{{ $m->display_order }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
                                    <input type="text" name="title" value="{{ $m->title }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Story</label>
                                <textarea name="content" rows="3" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary resize-none">{{ $m->content }}</textarea>
                            </div>

                            {{-- Existing photos with remove --}}
                            @if($m->photos->count() > 0)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Current Photos</label>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach($m->photos as $photo)
                                            <div class="photo-thumb">
                                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="">
                                                <div class="actions">
                                                    <button type="button" class="btn-crop" title="Adjust how guests see it" onclick="adminCropOpen('{{ asset('storage/' . $photo->file_path) }}?v={{ $photo->updated_at?->timestamp }}', 'cropForm{{ $photo->id }}')">✂</button>
                                                    <button type="button" class="btn-del" title="Remove" onclick="removePhoto({{ $photo->id }}, 'Remove?')">✕</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Add new photos --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Add Photos (click preview to crop)</label>
                                <div id="previews-{{ $m->id }}" class="flex gap-2 flex-wrap mb-2"></div>
                                <input type="file" data-previews="previews-{{ $m->id }}" name="images[]" multiple accept="image/*" class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-light">Save</button>
                                <button type="button" onclick="showView({{ $m->id }})" class="px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-secondary/10 p-8 text-center text-gray-400">
                <div class="text-4xl mb-3 text-gray-300"><i class="bi bi-book"></i></div>
                <p>No milestones yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Shared photo-delete form: lives OUTSIDE every other form so browsers can't merge it --}}
    <form id="photoDeleteForm" action="" method="POST" class="hidden" aria-hidden="true">
        @csrf @method('DELETE')
    </form>

    {{-- Hidden re-crop forms (one per photo, kept outside every other form) --}}
    <div class="hidden" aria-hidden="true">
        @foreach($milestones->flatMap->photos as $photo)
            <form id="cropForm{{ $photo->id }}" action="{{ route('admin.photos.crop', ['type' => 'story', 'id' => $photo->id]) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="x" value="0"><input type="hidden" name="y" value="0">
                <input type="hidden" name="width" value="0"><input type="hidden" name="height" value="0">
            </form>
        @endforeach
    </div>

    {{-- RIGHT: Add New Milestone --}}
    <div>
        <h3 class="font-semibold text-primary mb-4"><i class="bi bi-stars mr-1.5 text-secondary"></i>Add New Milestone</h3>

        <div class="bg-white rounded-xl border border-secondary/10 p-6 shadow-sm">
            <form action="{{ route('admin.story.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                            <input type="number" name="display_order" value="{{ $milestones->count() + 1 }}" min="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" required placeholder="e.g., How We Met" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Story *</label>
                        <textarea name="content" rows="4" required placeholder="Tell the story..." class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Photos (click preview to crop)</label>
                        <div id="previews-new" class="flex gap-2 flex-wrap mb-2"></div>
                        <input type="file" data-previews="previews-new" name="images[]" multiple accept="image/*" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-secondary/10 file:text-secondary hover:file:bg-secondary/20">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — up to 6 photos, Max 5MB each</p>
                    </div>
                    <button type="submit" class="w-full bg-secondary hover:bg-secondary-light text-primary font-semibold py-2.5 rounded-lg text-sm inline-flex items-center justify-center gap-2">Add Milestone <i class="bi bi-stars"></i></button>
                </div>
            </form>
        </div>

        <div class="mt-6">
            <a href="{{ route('story') }}" target="_blank" class="text-secondary hover:text-secondary/80 text-sm font-medium inline-flex items-center gap-1.5"><i class="bi bi-eye"></i> Preview Story Page <i class="bi bi-box-arrow-up-right"></i></a>
        </div>
    </div>
</div>

{{-- CROP MODAL --}}
<div id="cropModal" style="display:none;" class="crop-overlay" onclick="closeCrop(event)">
    <div class="crop-box" onclick="event.stopPropagation()">
        <h3 class="font-semibold text-primary text-lg mb-3"><i class="bi bi-scissors mr-1.5 text-secondary"></i>Crop Photo</h3>
        <div class="crop-preview">
            <img id="cropImage" src="">
        </div>
        <div class="flex justify-between items-center mt-4">
            <div class="flex gap-2">
                <button onclick="setRatio(NaN)" class="ratio-btn active" id="ratio-free">Free</button>
                <button onclick="setRatio(16/9)" class="ratio-btn" id="ratio-16">16:9</button>
                <button onclick="setRatio(4/3)" class="ratio-btn" id="ratio-43">4:3</button>
                <button onclick="setRatio(1)" class="ratio-btn" id="ratio-11">1:1</button>
            </div>
            <div class="flex gap-2">
                <button onclick="closeCrop()" class="px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                <button onclick="applyCrop()" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-light inline-flex items-center gap-1.5">Apply <i class="bi bi-scissors"></i></button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let cropper = null;
let cropTarget = null; // { inputEl, index, previewEl }

// Delete a story photo via the shared hidden form (avoids nested-form breakage)
function removePhoto(photoId, message) {
    if (!confirm(message)) return;
    const form = document.getElementById('photoDeleteForm');
    form.action = '{{ route('admin.story.photo.destroy', ':photoId') }}'.replace(':photoId', photoId);
    form.submit();
}

// Show/hide edit mode
function showEdit(id) {
    document.getElementById('view-' + id).style.display = 'none';
    document.getElementById('edit-' + id).style.display = 'block';
}
function showView(id) {
    document.getElementById('view-' + id).style.display = 'block';
    document.getElementById('edit-' + id).style.display = 'none';
}

// File input → preview with crop/remove buttons
document.querySelectorAll('input[data-previews]').forEach(input => {
    input.addEventListener('change', function() {
        const container = document.getElementById(this.dataset.previews);
        container.innerHTML = '';
        const files = Array.from(this.files);
        const inputEl = this;

        files.forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const thumb = document.createElement('div');
            thumb.className = 'photo-thumb';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.cursor = 'pointer';
            img.title = 'Click to crop';

            const actions = document.createElement('div');
            actions.className = 'actions';
            actions.style.display = 'flex';

            // Remove button
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn-del';
            delBtn.textContent = '✕';
            delBtn.title = 'Remove';
            delBtn.onclick = function() {
                thumb.remove();
                const dt = new DataTransfer();
                files.forEach((f, i) => { if (i !== index) dt.items.add(f); });
                inputEl.files = dt.files;
            };

            // Crop button
            const cropBtn = document.createElement('button');
            cropBtn.type = 'button';
            cropBtn.className = 'btn-crop';
            cropBtn.textContent = '✂';
            cropBtn.title = 'Crop';
            cropBtn.onclick = function() {
                openCrop(img.src, function(croppedBlob) {
                    const newFile = new File([croppedBlob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    files.forEach((f, i) => {
                        dt.items.add(i === index ? newFile : f);
                    });
                    inputEl.files = dt.files;
                    img.src = URL.createObjectURL(croppedBlob);
                });
            };

            actions.appendChild(delBtn);
            actions.appendChild(cropBtn);
            thumb.appendChild(img);
            thumb.appendChild(actions);
            container.appendChild(thumb);
        });
    });
});

// Crop functions
function openCrop(src, callback) {
    cropTarget = callback;
    document.getElementById('cropImage').src = src;
    document.getElementById('cropModal').style.display = 'flex';

    const img = document.getElementById('cropImage');
    img.onload = function() {
        if (cropper) cropper.destroy();
        cropper = new Cropper(img, {
            viewMode: 1,
            autoCropArea: 0.95,          // start with almost the whole photo so faces stay in
            background: false,
            // no forced aspect ratio — Free by default, nothing gets cut off
        });
        document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('ratio-free').classList.add('active');
    };
}

function setRatio(ratio) {
    document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove('active'));
    if (isNaN(ratio)) document.getElementById('ratio-free').classList.add('active');
    else if (ratio === 16/9) document.getElementById('ratio-16').classList.add('active');
    else if (ratio === 4/3) document.getElementById('ratio-43').classList.add('active');
    else document.getElementById('ratio-11').classList.add('active');
    if (!cropper) return;
    cropper.setAspectRatio(ratio);
    // Faces are usually in the upper part of a photo: slide the crop box toward the top
    try {
        const canvas = cropper.getCanvasData();
        const box = cropper.getCropBoxData();
        const top = Math.max(canvas.top, 0);
        if (box.top > top + 4) {
            cropper.setCropBoxData({ left: box.left, top: top, width: box.width, height: box.height });
        }
    } catch (err) { /* cosmetic only */ }
}

function applyCrop() {
    if (!cropper) return;
    cropper.getCroppedCanvas({ maxWidth: 1600, maxHeight: 1200 }).toBlob(function(blob) {
        if (cropTarget) cropTarget(blob);
        closeCrop();
    }, 'image/jpeg', 0.9);
}

function closeCrop(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('cropModal').style.display = 'none';
    if (cropper) { cropper.destroy(); cropper = null; }
    cropTarget = null;
}
</script>
@endpush
