<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic meta from wedding settings --}}
    <title>{{ $wedding->meta_title ?? $wedding->full_title ?? 'Wedding Celebration' }}</title>
    <meta name="description" content="{{ $wedding->meta_description ?? '' }}">

    {{-- Open Graph / WhatsApp sharing --}}
    <meta property="og:title" content="{{ $wedding->meta_title ?? $wedding->full_title ?? 'Wedding Celebration' }}">
    <meta property="og:description" content="{{ $wedding->meta_description ?? 'Join us in celebrating our wedding!' }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ $wedding->meta_image_path ? asset('storage/' . $wedding->meta_image_path) : '' }}">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $wedding->primary_color ?? "#1a3c2a" }}',
                        secondary: '{{ $wedding->secondary_color ?? "#c9a84c" }}',
                        ivory: '{{ $wedding->bg_color ?? "#fdf8f0" }}',
                        accent: '{{ $wedding->accent_color ?? "#f0e0d0" }}',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .font-playfair { font-family: 'Playfair Display', Georgia, serif; }

        /* Decorative divider */
        .ornament::before, .ornament::after {
            content: '✦';
            color: #c9a84c;
            margin: 0 1rem;
            font-size: 0.75rem;
        }

        /* Fade-in animation */
        .fade-in { animation: fadeIn 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ═══ Wedding preloader — monogram showcase ═══ */
        #wedding-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
            overflow: hidden;
            background:
                radial-gradient(1100px 520px at 88% -8%, rgba(201,168,76,0.16), transparent 60%),
                radial-gradient(900px 520px at -10% 112%, rgba(201,168,76,0.10), transparent 55%),
                linear-gradient(165deg, var(--ldr-bg, #1a3c2a) 0%, #0b1f14 135%);
            transition: opacity 0.7s ease;
        }
        #wedding-loader.is-done {
            opacity: 0;
            pointer-events: none;
        }

        /* golden halo behind the card */
        .ldr-halo {
            position: absolute;
            left: 50%;
            top: 44%;
            width: min(600px, 130vw);
            aspect-ratio: 1;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, rgba(201,168,76,0.20), rgba(201,168,76,0.05) 45%, transparent 70%);
            filter: blur(6px);
            pointer-events: none;
            animation: ldrHalo 6s ease-in-out infinite;
        }

        /* 3D scene */
        .ldr-scene {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 560px;
        }
        .ldr-card-float {
            position: relative;
            width: min(300px, 72vw);
            animation:
                ldrIn 1s cubic-bezier(0.2, 0.9, 0.3, 1.15) both,
                ldrFloat 6.5s ease-in-out 1.1s infinite;
        }
        .ldr-card {
            position: relative;
            width: 100%;
            aspect-ratio: 420 / 560;
            transform-style: preserve-3d;
            will-change: transform;
        }
        .ldr-card-face {
            position: absolute;
            inset: 0;
            border-radius: 14px;
            backface-visibility: hidden;
        }
        .ldr-card-front {
            transform: translateZ(28px);
            overflow: hidden;
            box-shadow:
                0 30px 60px rgba(0,0,0,0.45),
                inset 0 0 0 1px rgba(255,255,255,0.08),
                0 0 44px rgba(201,168,76,0.14);
        }
        .ldr-card-img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* stacked paper layers peeking out behind the card */
        .ldr-card-sheet {
            background: #90967e;
        }
        .ldr-card-sheet.s1 { transform: translateZ(20px) translate(3px, 3px); background: #90967e; }
        .ldr-card-sheet.s2 { transform: translateZ(12px) translate(6px, 6px); background: #878d75; }
        .ldr-card-sheet.s3 { transform: translateZ(4px)  translate(9px, 9px); background: #7e846c; }

        /* glossy floor reflection */
        .ldr-reflect {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 16px);
            pointer-events: none;
        }
        .ldr-reflect-inner {
            width: 100%;
            aspect-ratio: 420 / 560;
            transform: scaleY(-1);
            opacity: 0.26;
            filter: blur(3px);
            border-radius: 14px;
            overflow: hidden;
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent 72%);
            mask-image: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent 72%);
        }
        .ldr-reflect-inner .ldr-card-img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Copy */
        .ldr-copy {
            position: relative;
            margin-top: 26px;
        }
        .ldr-names {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(20px, 4.6vw, 26px);
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.02em;
            animation: ldrUp 0.9s ease-out 0.15s both;
        }
        .ldr-names em {
            color: var(--ldr-gold, #c9a84c);
            font-style: italic;
            margin: 0 0.5rem;
            font-weight: 400;
        }
        .ldr-rule {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--ldr-gold, #c9a84c);
            font-size: 13px;
            margin: 18px 0 14px;
            opacity: 0.8;
            animation: ldrUp 0.9s ease-out 0.35s both;
        }
        .ldr-rule span {
            width: 44px;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--ldr-gold, #c9a84c));
        }
        .ldr-rule span:last-child {
            background: linear-gradient(to left, transparent, var(--ldr-gold, #c9a84c));
        }
        .ldr-tag {
            font-size: 10px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
            animation: ldrUp 0.9s ease-out 0.5s both;
        }
        .ldr-progress {
            width: 170px;
            height: 3px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            overflow: hidden;
            margin-top: 26px;
            animation: ldrUp 0.9s ease-out 0.65s both;
        }
        .ldr-progress span {
            display: block;
            width: 34%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, rgba(201,168,76,0.5), var(--ldr-gold, #c9a84c));
            box-shadow: 0 0 10px rgba(201,168,76,0.7);
            animation: ldrSlide 1.3s ease-in-out infinite;
        }
        @keyframes ldrSlide {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(420%); }
        }
        @keyframes ldrUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 3D wedding icons popping out around the card */
        .ldr-icons {
            position: absolute;
            inset: 0;
            pointer-events: none;
            perspective: 900px;
            z-index: 4;
        }
        .ldr-ic {
            position: absolute;
            font-style: normal;
            opacity: 0;
            transform: translate(-50%, -50%);
            animation: ldrIconPop var(--d) cubic-bezier(0.18, 1.3, 0.4, 1) var(--delay) both;
        }
        .ldr-ic .bi {
            display: block;
            font-size: var(--sz);
            color: var(--c);
            filter: drop-shadow(0 6px 14px rgba(0,0,0,0.35)) drop-shadow(0 0 8px rgba(201,168,76,0.4));
            transform-style: preserve-3d;
            animation: ldrIconSpin var(--s) linear var(--delay) infinite;
        }
        @keyframes ldrIconPop {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0) translateY(34px); }
            55% { opacity: 1; }
            100% { opacity: 1; transform: translate(-50%, -50%) scale(1) translateY(0); }
        }
        @keyframes ldrIconSpin {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        /* floating dust & sparkles */
        .ldr-dust {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .ldr-dust i {
            position: absolute;
            font-style: normal;
            color: var(--ldr-gold, #c9a84c);
            opacity: 0;
            animation: ldrDrift var(--dur) ease-in-out var(--delay) infinite;
        }
        .ldr-dust i.dot {
            width: var(--sz);
            height: var(--sz);
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
            font-size: 0;
        }
        .ldr-dust i.glyph {
            color: rgba(255,255,255,0.85);
            text-shadow: 0 0 8px rgba(201,168,76,0.85);
        }
        @keyframes ldrDrift {
            0%, 100% { opacity: 0; transform: translate(0, 0) scale(0.5) rotate(0deg); }
            20% { opacity: var(--o); }
            50% { transform: translate(var(--dx), var(--dy)) scale(1) rotate(var(--rot)); }
            80% { opacity: 0; }
        }
        @keyframes ldrIn {
            from { opacity: 0; transform: translateY(44px) scale(0.9); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes ldrFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes ldrHalo {
            0%, 100% { opacity: 0.7; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.08); }
        }
        @media (prefers-reduced-motion: reduce) {
            .ldr-card-float, .ldr-halo, .ldr-progress span { animation: none; }
            .ldr-dust, .ldr-icons { display: none; }
        }

        {{-- ═══ Hero photo: desktop / tablet / phone ═══ --}}
        .hero-viewport {
            min-height: 100vh;
        }
        @supports (min-height: 100svh) {
            .hero-viewport { min-height: 100svh; } /* phone URL-bar safe */
        }
        .hero-photo {
            transform-origin: center;
            animation: heroZoom 22s ease-in-out infinite alternate;
        }
        @keyframes heroZoom {
            from { transform: scale(1.02); }
            to   { transform: scale(1.12); }
        }
        @media (max-width: 1023px) {
            /* On phones/tablets the names + countdown sit on the photo, so keep it slightly less zoomed */
            .hero-photo { animation-duration: 30s; }
        }
        @media (prefers-reduced-motion: reduce) {
            .hero-photo { animation: none; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-ivory text-charcoal min-h-screen">

    {{-- ═══ Wedding preloader — 3D monogram showcase ═══ --}}
    <div id="wedding-loader"
         style="--ldr-bg: {{ $wedding->primary_color ?? '#1a3c2a' }}; --ldr-gold: {{ $wedding->secondary_color ?? '#c9a84c' }};">
        <div class="ldr-halo" aria-hidden="true"></div>
        <div class="ldr-scene">
            <div class="ldr-card-float">
                <div class="ldr-card" id="ldr-card">
                    <div class="ldr-card-face ldr-card-sheet s3"></div>
                    <div class="ldr-card-face ldr-card-sheet s2"></div>
                    <div class="ldr-card-face ldr-card-sheet s1"></div>
                    <div class="ldr-card-face ldr-card-front">
                        <img src="{{ asset('images/loading-card.jpg') }}" alt="Netta & Jeff" class="ldr-card-img">
                    </div>
                </div>
                <div class="ldr-reflect" aria-hidden="true">
                    <div class="ldr-reflect-inner">
                        <img src="{{ asset('images/loading-card.jpg') }}" alt="" aria-hidden="true" class="ldr-card-img">
                    </div>
                </div>
            </div>
            <div class="ldr-copy">
                <p class="ldr-names">{{ $wedding->bride_name ?? 'Netta' }}<em>&amp;</em>{{ $wedding->groom_name ?? 'Jeff' }}</p>
                <div class="ldr-rule"><span></span><i class="bi bi-gem"></i><span></span></div>
                <p class="ldr-tag">Opening the celebration…</p>
                <div class="ldr-progress"><span></span></div>
            </div>
            <div class="ldr-dust" id="ldr-dust" aria-hidden="true"></div>
            <div class="ldr-icons" id="ldr-icons" aria-hidden="true"></div>
        </div>
    </div>
    <script>
        (function () {
            var loader = document.getElementById('wedding-loader');
            if (!loader) return;

            /* ---------- hide timing (kept from original) ---------- */
            var started = Date.now();
            var hidden = false;
            function hide() {
                if (hidden) return;
                hidden = true;
                var remaining = 1500 - (Date.now() - started);
                setTimeout(function () {
                    loader.classList.add('is-done');
                setTimeout(function () {
                    if (loader.parentNode) loader.parentNode.removeChild(loader);
                    try { window.dispatchEvent(new Event('wedding:loader-done')); } catch (e) {}
                }, 750);
                }, Math.max(0, remaining));
            }
            if (document.readyState === 'complete') { hide(); }
            else { window.addEventListener('load', hide); }
            setTimeout(hide, 3800); // safety fallback

            /* ---------- floating dust & sparkles ---------- */
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var dust = document.getElementById('ldr-dust');
            if (dust && !reduce) {
                var glyphs = ['✦', '✧', '❋', '+'];
                for (var i = 0; i < 26; i++) {
                    var el = document.createElement('i');
                    var isGlyph = i % 7 === 0;
                    el.className = isGlyph ? 'glyph' : 'dot';
                    if (isGlyph) {
                        el.textContent = glyphs[i % glyphs.length];
                        el.style.fontSize = (8 + Math.random() * 10) + 'px';
                    } else {
                        el.style.setProperty('--sz', (2 + Math.random() * 4) + 'px');
                    }
                    el.style.left = (4 + Math.random() * 92) + '%';
                    el.style.top = (8 + Math.random() * 84) + '%';
                    el.style.setProperty('--dur', (4 + Math.random() * 5).toFixed(2) + 's');
                    el.style.setProperty('--delay', (Math.random() * 5).toFixed(2) + 's');
                    el.style.setProperty('--o', (0.25 + Math.random() * 0.5).toFixed(2));
                    el.style.setProperty('--dx', ((Math.random() - 0.5) * 70).toFixed(0) + 'px');
                    el.style.setProperty('--dy', (-20 - Math.random() * 60).toFixed(0) + 'px');
                    el.style.setProperty('--rot', ((Math.random() - 0.5) * 180).toFixed(0) + 'deg');
                    dust.appendChild(el);
                }
            }

            /* ---------- wedding icons popping out in 3D ---------- */
            var icons = document.getElementById('ldr-icons');
            if (icons && !reduce) {
                var set = ['bi-heart-fill', 'bi-gem', 'bi-flower1', 'bi-stars', 'bi-envelope-heart', 'bi-brightness-high'];
                for (var i = 0; i < 12; i++) {
                    var wrap = document.createElement('i');
                    wrap.className = 'ldr-ic';
                    var inner = document.createElement('i');
                    inner.className = 'bi ' + set[i % set.length];
                    wrap.appendChild(inner);

                    /* ring placement around the card, biased to the outer edges */
                    var ang = (i / 12) * Math.PI * 2 + Math.random() * 0.5;
                    var rad = 36 + Math.random() * 9;
                    wrap.style.left = (50 + Math.cos(ang) * rad) + '%';
                    wrap.style.top = (44 + Math.sin(ang) * rad) + '%';
                    wrap.style.setProperty('--sz', (15 + Math.random() * 14) + 'px');
                    wrap.style.setProperty('--c', i % 3 === 0 ? '#ffffff' : 'var(--ldr-gold, #c9a84c)');
                    wrap.style.setProperty('--d', (0.5 + Math.random() * 0.4).toFixed(2) + 's');
                    wrap.style.setProperty('--delay', (0.15 + Math.random() * 0.9).toFixed(2) + 's');
                    wrap.style.setProperty('--s', (3 + Math.random() * 3).toFixed(1) + 's');
                    icons.appendChild(wrap);
                }
            }

            /* ---------- 3D tilt following the pointer ---------- */
            var card = document.getElementById('ldr-card');
            if (card && !reduce) {
                var tx = 0, ty = 0, cx = 0, cy = 0;
                var lastMove = 0;
                var raf = null;

                function onMove(e) {
                    var r = loader.getBoundingClientRect();
                    var px = (e.clientX - r.left) / r.width - 0.5;
                    var py = (e.clientY - r.top) / r.height - 0.5;
                    tx = -py * 16;
                    ty = px * 18;
                    lastMove = Date.now();
                }

                function loop() {
                    /* idle: gentle auto-sway */
                    if (Date.now() - lastMove > 1400) {
                        var t = Date.now() / 1000;
                        tx = Math.sin(t * 0.55) * 4;
                        ty = Math.cos(t * 0.4) * 5;
                    }
                    cx += (tx - cx) * 0.08;
                    cy += (ty - cy) * 0.08;
                    card.style.transform = 'perspective(1100px) rotateX(' + cx.toFixed(2) + 'deg) rotateY(' + cy.toFixed(2) + 'deg)';
                    raf = requestAnimationFrame(loop);
                }

                loader.addEventListener('pointermove', onMove, { passive: true });
                raf = requestAnimationFrame(loop);
            }
        })();
    </script>

    {{-- Desktop Navigation — one-page scrollspy + the photo album --}}
    <nav class="hidden md:block fixed top-0 left-0 right-0 z-50 bg-primary/95 backdrop-blur-sm border-b border-secondary/20 transition-shadow duration-500" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50" :class="scrolled ? 'shadow-2xl shadow-black/30' : ''">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-secondary font-playfair text-xl font-semibold tracking-wide">
                {{ $wedding->bride_name ?? 'Bride' }} & {{ $wedding->groom_name ?? 'Groom' }}
            </a>
            <div class="flex items-center gap-5">
                @foreach([
                    ['href' => '#story', 'label' => 'Our Story'],
                    ['href' => '#schedule', 'label' => 'Schedule'],
                    ['href' => '#location', 'label' => 'Location'],
                    ['href' => '#rsvp', 'label' => 'RSVP'],
                    ['href' => '#guestbook', 'label' => 'Wishes'],
                ] as $item)
                    <a href="{{ $item['href'] }}" data-navlink="{{ ltrim($item['href'], '#') }}"
                       class="nav-section-link text-[13px] tracking-wider uppercase transition-colors duration-300 text-white/70 hover:text-secondary">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <span class="w-px h-4 bg-white/15"></span>
                <a href="{{ route('gallery') }}"
                   class="inline-flex items-center gap-1.5 text-[13px] tracking-wider uppercase transition-all duration-300 px-3.5 py-1.5 rounded-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-primary">
                    <i class="bi bi-images"></i> Album
                </a>
                <span class="w-px h-4 bg-white/15"></span>
                <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" title="Admin"
                   aria-label="Admin area"
                   class="text-white/40 hover:text-secondary transition-colors duration-300">
                    <i class="bi bi-gear text-[16px]"></i>
                </a>
            </div>
        </div>
    </nav>

    {{-- Discreet admin gear (mobile) — lands on the dashboard when signed in, the login page otherwise --}}
    <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" title="Admin"
       aria-label="Admin area"
       class="md:hidden fixed top-3 right-3 z-40 w-9 h-9 rounded-full bg-primary/70 text-secondary/80 border border-secondary/25 backdrop-blur flex items-center justify-center transition-all duration-200 hover:text-secondary active:scale-90">
        <i class="bi bi-gear text-[15px]"></i>
    </a>

    {{-- Main Content --}}
    <main class="pb-28 md:pb-0 pt-0">
        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation — sections + the photo album --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50">
        <div class="relative bg-primary/95 backdrop-blur-xl border-t border-secondary/20" style="padding-bottom: env(safe-area-inset-bottom, 0px);">
            <div class="flex items-end justify-between h-16 px-1">
                @foreach([
                    ['href' => '#top', 'icon' => 'bi-house-heart', 'label' => 'Home'],
                    ['href' => '#story', 'icon' => 'bi-book-heart', 'label' => 'Story'],
                    ['href' => '#rsvp', 'icon' => 'bi-envelope-heart-fill', 'label' => 'RSVP', 'center' => true],
                    ['href' => '#guestbook', 'icon' => 'bi-chat-heart', 'label' => 'Wishes'],
                    ['href' => url('') . '/gallery', 'icon' => 'bi-images', 'label' => 'Album', 'page' => true],
                ] as $item)
                    @if(!empty($item['center']))
                        {{-- Raised center action: RSVP --}}
                        <a href="{{ $item['href'] }}"
                           class="group relative flex-1 h-full flex flex-col items-center justify-end pb-1.5 text-primary">
                            <span class="absolute left-1/2 -translate-x-1/2 -top-7 w-16 h-16 rounded-full bg-gradient-to-b from-secondary via-[#dcbc63] to-[#ad8c39] ring-4 ring-ivory shadow-[0_10px_24px_rgba(201,168,76,0.45)] flex items-center justify-center transition-transform duration-300 group-hover:scale-105 group-active:scale-90">
                                <i class="bi {{ $item['icon'] }} text-[24px] text-primary drop-shadow-sm"></i>
                            </span>
                            <span class="relative text-[9px] uppercase tracking-wider font-semibold text-secondary">{{ $item['label'] }}</span>
                        </a>
                    @else
                        <a href="{{ $item['href'] }}"
                           class="group flex-1 h-full flex flex-col items-center justify-center gap-1 transition-colors {{ !empty($item['page']) && request()->routeIs('gallery') ? 'text-secondary' : 'text-white/50 hover:text-white/85' }}">
                            <i class="bi {{ $item['icon'] }} text-[22px] leading-none transition-transform duration-300 {{ (!empty($item['page']) && request()->routeIs('gallery')) ? '-translate-y-0.5' : 'group-hover:-translate-y-0.5' }}"></i>
                            <span class="uppercase whitespace-nowrap text-[9px] tracking-wider {{ (!empty($item['page']) && request()->routeIs('gallery')) ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </nav>

    {{-- Footer --}}
    <footer class="hidden md:block bg-primary text-white/70 py-8">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <p class="font-playfair text-secondary text-lg">{{ $wedding->bride_name ?? '' }} & {{ $wedding->groom_name ?? '' }}</p>
            <p class="mt-2 text-sm">{{ $wedding->wedding_date?->format('F j, Y') ?? '' }}</p>
            <p class="mt-4 text-xs text-white/40">Made with <i class="bi bi-heart-fill text-secondary"></i> for a beautiful celebration</p>
        </div>
    </footer>

    @stack('scripts')

    @if(request()->routeIs('home'))
    {{-- ═══ One-page scrollspy: highlights the nav item of the section in view ═══ --}}
    <script>
    (function () {
        var links = document.querySelectorAll('.nav-section-link');
        if (!links.length) return;

        function setActive(id) {
            links.forEach(function (l) {
                l.classList.toggle('text-secondary', l.getAttribute('data-navlink') === id);
                l.classList.toggle('font-semibold', l.getAttribute('data-navlink') === id);
                l.classList.toggle('text-white/70', l.getAttribute('data-navlink') !== id);
            });
        }

        var sections = Array.prototype.slice.call(document.querySelectorAll('section[id]'));
        if (!('IntersectionObserver' in window)) return;

        var current = '';
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) current = entry.target.id;
            });
            setActive(current || '');
        }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });

        sections.forEach(function (s) { io.observe(s); });
        setActive('');
    })();
    </script>
    @endif
</body>
</html>
