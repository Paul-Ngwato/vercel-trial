<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ $wedding->full_title ?? 'Wedding Hub' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a3c2a',
                        'primary-light': '#245238',
                        'primary-dark': '#122b1e',
                        'primary-darker': '#0d1f15',
                        secondary: '#c9a84c',
                        'secondary-light': '#d4b96e',
                        'secondary-dark': '#b8942f',
                        ivory: '#fdf8f0',
                        accent: '#f0e0d0',
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { transform: translateX(2px); }
        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(201,168,76,0.15), rgba(201,168,76,0.05));
            border-left: 3px solid #c9a84c;
        }
        .sidebar-link.active svg { color: #c9a84c; }
        .sidebar-link.active span { color: #fff; font-weight: 600; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .topbar-blur { backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%); }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f5f5f7] min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- ═══ SIDEBAR ═══ --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm" @click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-[260px] bg-primary-darker flex flex-col transform transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           style="background: linear-gradient(180deg, #122b1e 0%, #0d1f15 50%, #0a1a10 100%);">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-white/[0.06]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-secondary to-secondary-dark flex items-center justify-center text-lg shadow-lg shadow-secondary/20 group-hover:shadow-secondary/30 transition-shadow"><i class="bi bi-gem text-primary"></i></div>
                <div class="min-w-0">
                    <h1 class="font-bold text-[13px] text-white leading-tight">Wedding Hub</h1>
                    <p class="text-white/30 text-[10px] mt-0.5 truncate">{{ $wedding->couple_names ?? 'Dashboard' }}</p>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">
            @php
                $navGroups = [
                    'OVERVIEW' => [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-fill'],
                    ],
                    'GUESTS' => [
                        ['route' => 'admin.rsvps', 'label' => 'Guests & RSVPs', 'icon' => 'bi-people-fill'],
                        ['route' => 'admin.guestbook', 'label' => 'Well Wishes', 'icon' => 'bi-chat-heart-fill'],
                        ['route' => 'admin.photos', 'label' => 'Guest Photos', 'icon' => 'bi-images'],
                    ],
                    'WEBSITE' => [
                        ['route' => 'admin.story', 'label' => 'Our Story', 'icon' => 'bi-journal-bookmark-fill'],
                        ['route' => 'admin.events', 'label' => 'Schedule', 'icon' => 'bi-calendar2-week-fill'],
                        ['route' => 'admin.information', 'label' => 'Guest Info', 'icon' => 'bi-card-list'],
                        ['route' => 'admin.popup-photos', 'label' => 'Popup Photos', 'icon' => 'bi-camera-reels-fill'],
                    ],
                    'SYSTEM' => [
                        ['route' => 'admin.notifications', 'label' => 'Activity', 'icon' => 'bi-bell-fill'],
                        ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => 'bi-gear-fill'],
                    ],
                ];
            @endphp

            @foreach($navGroups as $group => $items)
                <div>
                    <p class="text-[10px] font-semibold text-white/20 uppercase tracking-[0.15em] px-3 mb-1.5">{{ $group }}</p>
                    <div class="space-y-0.5">
                        @foreach($items as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] {{ $isActive ? 'active' : 'text-white/45 hover:text-white/80 hover:bg-white/[0.04]' }}">
                                <i class="bi {{ $item['icon'] }} text-[17px] leading-none flex-shrink-0 {{ $isActive ? 'text-secondary' : 'text-white/30' }}"></i>
                                <span>{{ $item['label'] }}</span>
                                @if($item['route'] === 'admin.notifications')
                                    @php
                                        $pendingTotal = (\App\Models\Photo::pending()->count() ?? 0) + (\App\Models\GuestbookMessage::pending()->count() ?? 0);
                                    @endphp
                                    @if($pendingTotal > 0)
                                        <span class="ml-auto text-[9px] bg-red-500/90 text-white px-1.5 py-[2px] rounded-full font-bold leading-none">{{ $pendingTotal > 99 ? '99+' : $pendingTotal }}</span>
                                    @endif
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Bottom section --}}
        <div class="px-3 py-3 border-t border-white/[0.06]">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[12px] text-white/25 hover:text-white/60 hover:bg-white/[0.04] transition-colors mb-1">
                <i class="bi bi-box-arrow-up-right"></i>
                View Website
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[12px] text-white/25 hover:text-red-400 hover:bg-red-500/[0.06] transition-colors">
                    <i class="bi bi-box-arrow-right"></i>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="lg:ml-[260px] min-h-screen">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 topbar-blur bg-white/70 border-b border-gray-200/60 px-4 sm:px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-list text-lg text-gray-500"></i>
                </button>
                <div>
                    <h2 class="text-[15px] font-bold text-gray-800">{{ $pageTitle ?? 'Dashboard' }}</h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.notifications') }}" class="relative w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-bell-fill text-[15px] text-gray-500"></i>
                    @php $pendingCount = (\App\Models\Photo::pending()->count() ?? 0) + (\App\Models\GuestbookMessage::pending()->count() ?? 0); @endphp
                    @if($pendingCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center border-2 border-white">{{ $pendingCount > 9 ? '9+' : $pendingCount }}</span>
                    @endif
                </a>
                <div class="w-px h-5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-2 cursor-default">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white text-[10px] font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                    <span class="text-[12px] font-medium text-gray-500 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    {{-- ═══ Shared photo re-crop modal (opened by the ✂ button on any photo) ═══ --}}
    <div id="adminCropModal" style="display:none;" class="fixed inset-0 z-[999] bg-black/70 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-2xl">
            <h3 class="font-semibold text-gray-800 text-lg mb-3"><i class="bi bi-scissors mr-1.5 text-secondary"></i>Adjust how guests see this photo</h3>
            <p class="text-xs text-gray-400 mb-3 -mt-2">Drag the box to choose what stays in frame. The original is always kept safe, so you can re-crop again later.</p>
            <div class="max-h-[55vh] overflow-hidden rounded-xl bg-gray-100">
                <img id="adminCropImage" src="" class="block max-w-full">
            </div>
            <div class="flex flex-wrap justify-between items-center gap-3 mt-4">
                <div class="flex gap-2">
                    <button type="button" onclick="adminCropRatio(NaN)" class="ratio-btn2 active px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">Free</button>
                    <button type="button" onclick="adminCropRatio(4/3)" class="ratio-btn2 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">4:3</button>
                    <button type="button" onclick="adminCropRatio(1)" class="ratio-btn2 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">1:1</button>
                    <button type="button" onclick="adminCropRatio(16/9)" class="ratio-btn2 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">16:9</button>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="adminCropClose()" class="px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                    <button type="button" onclick="adminCropApply()" class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary-light inline-flex items-center gap-1.5"><i class="bi bi-check-lg"></i> Save crop</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
    let adminCropper = null, adminCropTarget = null;

    /* Open the crop modal for any photo. formId = the id of the small hidden
       form placed next to the photo's crop button; it already knows the URL. */
    function adminCropOpen(imgSrc, formId) {
        adminCropTarget = formId;
        const modal = document.getElementById('adminCropModal');
        const img = document.getElementById('adminCropImage');
        img.src = imgSrc;
        modal.style.display = 'flex';
        img.onload = function () {
            if (adminCropper) adminCropper.destroy();
            adminCropper = new Cropper(img, {
                viewMode: 1,
                autoCropArea: 0.95,
                background: false,
                /* start free — nothing gets cut unless the admin chooses to */
            });
            document.querySelectorAll('.ratio-btn2').forEach(b => b.classList.remove('active'));
            document.querySelector('.ratio-btn2').classList.add('active');
        };
    }

    function adminCropRatio(r) {
        document.querySelectorAll('.ratio-btn2').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        if (adminCropper) adminCropper.setAspectRatio(r);
    }

    function adminCropApply() {
        if (!adminCropper || !adminCropTarget) return;
        const form = document.getElementById(adminCropTarget);
        if (!form) return;
        const data = adminCropper.getData(true); // rounded, natural-image pixels
        form.querySelector('[name=x]').value = data.x;
        form.querySelector('[name=y]').value = data.y;
        form.querySelector('[name=width]').value = data.width;
        form.querySelector('[name=height]').value = data.height;
        form.submit();
    }

    function adminCropClose() {
        document.getElementById('adminCropModal').style.display = 'none';
        if (adminCropper) { adminCropper.destroy(); adminCropper = null; }
        adminCropTarget = null;
    }
    </script>

    @stack('scripts')
</body>
</html>
