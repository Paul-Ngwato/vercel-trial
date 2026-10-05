@extends('layouts.admin', ['pageTitle' => 'Information Management'])

@section('content')
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<div class="grid lg:grid-cols-5 gap-8">
    {{-- Existing Items --}}
    <div class="lg:col-span-3">
        <h3 class="font-semibold text-primary mb-4 flex items-center gap-2">
            <i class="bi bi-clipboard-check-fill text-secondary"></i> Information Items
            <span class="text-xs font-normal text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $items->count() }}</span>
        </h3>

        @if($items->isEmpty())
            <div class="bg-white rounded-xl border border-secondary/10 p-8 text-center text-gray-400">
                <div class="text-4xl mb-3 text-gray-300"><i class="bi bi-clipboard-check-fill"></i></div>
                <p>No information items yet.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($items as $item)
                    <div class="bg-white rounded-xl border border-secondary/10 p-4 shadow-sm" x-data="{ edit: false }">
                        {{-- View --}}
                        <div x-show="!edit">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center text-xl flex-shrink-0"><i class="bi {{ \App\Support\Icons::bi($item->icon, 'bi-pin-map-fill') }} text-secondary"></i></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-primary">{{ $item->title }}</h4>
                                        @if(!$item->is_published)
                                            <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Hidden</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ $item->content }}</p>
                                </div>
                                <div class="flex gap-1 flex-shrink-0">
                                    <form action="{{ route('admin.information.toggle', $item) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded hover:bg-gray-100 {{ $item->is_published ? 'text-green-600' : 'text-gray-400' }}" title="{{ $item->is_published ? 'Published' : 'Hidden' }}">
                                            <i class="bi {{ $item->is_published ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                        </button>
                                    </form>
                                    <button @click="edit = true" class="text-secondary hover:text-secondary/80 text-xs px-2 py-1 rounded hover:bg-secondary/10"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('admin.information.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded hover:bg-red-50"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Edit --}}
                        <div x-show="edit" x-cloak>
                            <form action="{{ route('admin.information.update', $item) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-3">
                                    <div class="grid grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Icon</label>
                                            <input type="text" name="icon" value="{{ $item->icon }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm text-center outline-none focus:border-secondary">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
                                            <input type="text" name="title" value="{{ $item->title }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Order</label>
                                            <input type="number" name="display_order" value="{{ $item->display_order }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Content</label>
                                        <textarea name="content" rows="2" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary resize-none">{{ $item->content }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="is_published" value="1" {{ $item->is_published ? 'checked' : '' }} class="rounded border-gray-300 text-secondary focus:ring-secondary">
                                            Published
                                        </label>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-light">Save</button>
                                        <button type="button" @click="edit = false" class="px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Add New --}}
    <div class="lg:col-span-2">
        <h3 class="font-semibold text-primary mb-4 flex items-center gap-2">
            <i class="bi bi-plus-lg text-secondary"></i> Add Information
        </h3>

        <div class="bg-white rounded-xl border border-secondary/10 p-6 shadow-sm">
            <form action="{{ route('admin.information.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <input type="text" name="icon" value="📌" maxlength="10" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm text-center outline-none focus:border-secondary">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" required placeholder="e.g., Parking" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                        <textarea name="content" rows="3" required placeholder="Details about this topic..." class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" value="{{ $items->count() + 1 }}" min="0" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-secondary">
                    </div>
                    <button type="submit" class="w-full bg-secondary hover:bg-secondary-light text-primary font-semibold py-2.5 rounded-lg text-sm transition-all inline-flex items-center justify-center gap-2">Add Item <i class="bi bi-plus-circle"></i></button>
                </div>
            </form>
        </div>

        {{-- Quick emoji picker --}}
        <div class="mt-4 bg-white rounded-xl border border-secondary/10 p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500 mb-2">Quick Emojis</p>
            <div class="flex flex-wrap gap-1">
                @foreach(['👔','🚗','🚌','🏨','📸','👶','🎁','📞','🍽️','🎵','✈️','💰','🔑','📱','🎭','🎂','💐','⛪','🅿️','🌊'] as $e)
                    <button type="button" onclick="document.querySelector('[name=icon]').value='{{ $e }}'" class="w-8 h-8 rounded-lg hover:bg-secondary/10 flex items-center justify-center text-lg transition">{{ $e }}</button>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('information') }}" target="_blank" class="text-secondary hover:text-secondary/80 text-sm font-medium inline-flex items-center gap-1.5"><i class="bi bi-eye"></i> Preview Information Page <i class="bi bi-box-arrow-up-right"></i></a>
        </div>
    </div>
</div>
@endsection
