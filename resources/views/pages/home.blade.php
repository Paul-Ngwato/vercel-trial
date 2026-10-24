@extends('layouts.public')

@section('content')
<div id="top"></div>

{{-- ═══════════════════════ OPENING INTRO: envelope → reveal → popups ═══════════════════════ --}}
<div id="wedding-intro"
     style="--wbg: {{ $wedding->primary_color ?? '#1a3c2a' }}; --wgold: {{ $wedding->secondary_color ?? '#c9a84c' }};">

    {{-- Stage 1 — the envelope --}}
    <div id="intro-envelope-stage" class="intro-stage">
        <div id="intro-env-floaties" aria-hidden="true"></div>
        {{-- white petals drifting through the invitation moment --}}
        <div id="intro-petals" aria-hidden="true">
            @foreach (range(1, 22) as $i)
                <span class="intro-petal" style="--ix:{{ rand(2, 96) }}%;--id:{{ rand(7, 15) }}s;--idl:{{ rand(-14, 0) }}s;--ir:{{ rand(0, 360) }}deg;">{{ ['✿', '❀', '✾', '❁'][rand(0, 3)] }}</span>
            @endforeach
        </div>
        <p class="intro-eyebrow">You are cordially invited to the wedding of</p>
        <h2 class="env-names font-playfair">{{ $wedding->bride_name ?? 'Bride' }} <em>&amp;</em> {{ $wedding->groom_name ?? 'Groom' }}</h2>
        <p class="env-date">{{ $wedding->wedding_date?->format('F j, Y') ?? 'Save the date' }}</p>
        <div class="env-wrap">
            <div class="env-glow" aria-hidden="true"></div>
            <div id="intro-envelope" class="env-spiro-envelope" role="button" tabindex="0" aria-label="Open the invitation">
                @include('pages.partials.env-spiro')
            </div>
        </div>
        <p class="env-tap"><i class="bi bi-hand-index-thumb-fill"></i> Tap to open</p>
    </div>

    {{-- Stage 2 — tap-to-reveal photo --}}
    <div id="intro-photo-stage" class="intro-stage">
        <p class="intro-eyebrow">A memory awaits</p>
        <div class="scratch-frame">
            <div class="scratch-imgwrap">
                @php $introPhoto = $wedding->heroImages->first()?->image_path ?: $wedding->hero_image_path; @endphp
                @if($introPhoto)
                    <img src="{{ asset('storage/' . $introPhoto) }}" alt="A wedding memory" class="scratch-img">
                @else
                    <div class="scratch-fallback">
                        <i class="bi bi-camera-fill"></i>
                        <span class="font-playfair">A memory awaits</span>
                    </div>
                @endif
                <div id="reveal-cover" class="reveal-cover" role="button" tabindex="0" aria-label="Tap to reveal the photo">
                    <span class="reveal-cover-inner">
                        <i class="bi bi-hand-index-thumb-fill reveal-tap-icon"></i>
                        <span class="reveal-cover-text font-playfair">Tap to reveal</span>
                        <span class="reveal-cover-sub">a little magic awaits</span>
                    </span>
                </div>
                <div class="reveal-flash" aria-hidden="true"></div>
            </div>
            <div class="scratch-caption">
                <span class="scratch-names font-playfair">{{ $wedding->bride_name ?? 'Bride' }} &amp; {{ $wedding->groom_name ?? 'Groom' }}</span>
            </div>
        </div>
    </div>

    {{-- Popup photos — always inside the screen frame --}}    <div id="intro-popups" aria-hidden="true"></div>
    <button id="intro-replay" class="intro-replay" type="button" hidden><i class="bi bi-arrow-counterclockwise"></i> Replay intro</button>
    {{-- sound toggle removed at the couple's request — the engine stays muted; 
         to bring audio back, restore the button with id="sound-toggle" --}}
</div>

{{-- ═══════════════════════ HERO ═══════════════════════ --}}
<section id="hero" class="relative hero-viewport flex flex-col items-center overflow-hidden bg-primary">
    <div class="stars-container absolute inset-0" id="starsContainer"></div>
    <div class="absolute -top-44 left-1/2 -translate-x-1/2 w-[44rem] h-[44rem] rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-32 w-[30rem] h-[30rem] rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>

    {{-- Wedding florals: white rose petals drifting down the hero --}}
    <div id="flower-overlay" class="petal-field" aria-hidden="true">
        @php $flowerTypes = ['✿', '❀', '✾', '❁', '✾', '✿']; @endphp
        @foreach (range(1, 30) as $i)
            <span class="flower petal" style="--i:{{ $i }};--x:{{ rand(3, 97) }}%;--t:{{ rand(2, 12) }}s;--r:{{ rand(0, 360) }}deg;--d:{{ rand(8, 18) }}s;">
                {{ $flowerTypes[$i % count($flowerTypes)] }}
            </span>
        @endforeach
    </div>

    <div class="relative z-10 text-center px-6 pt-14 sm:pt-20 pb-6 w-full max-w-4xl mx-auto">
        <div class="flex items-center justify-center gap-4 mb-5 sm:mb-7">
            <div class="w-12 h-px bg-gradient-to-r from-transparent to-secondary/60"></div>
            <span class="text-secondary text-xl animate-pulse">✦</span>
            <div class="w-12 h-px bg-gradient-to-l from-transparent to-secondary/60"></div>
        </div>

        <p class="text-secondary/80 tracking-[0.3em] uppercase text-xs sm:text-sm mb-5">Together with their families</p>

        <h1 class="font-playfair text-white leading-tight">
            <span class="block text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-light drop-shadow-2xl">{{ $wedding->bride_name ?? 'Bride' }}</span>
            <span class="block text-secondary my-2 md:my-3 text-2xl sm:text-3xl md:text-4xl font-playfair italic drop-shadow-lg">&amp;</span>
            <span class="block text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-light drop-shadow-2xl">{{ $wedding->groom_name ?? 'Groom' }}</span>
        </h1>

        <div class="mt-5 sm:mt-7">
            <p class="text-secondary font-playfair text-xl sm:text-2xl md:text-3xl drop-shadow-lg">{{ $wedding->wedding_date?->format('l, F j, Y') ?? 'Date TBD' }}</p>
            @if($wedding->wedding_time)
                <p class="text-white/50 mt-2 text-sm sm:text-base">at {{ \Carbon\Carbon::parse($wedding->wedding_time)->format('g:i A') }}</p>
            @endif
        </div>

        @if($wedding->wedding_date && !$wedding->isPastWedding())
            <div x-data="countdown('{{ $wedding->wedding_date->toIso8601String() }}')" x-init="init()" class="mt-6 flex justify-center gap-3 sm:gap-6 md:gap-8">
                <div class="text-center"><div class="text-3xl sm:text-4xl md:text-5xl font-playfair text-secondary drop-shadow-lg" x-text="days">00</div><div class="text-white/50 text-[10px] sm:text-xs uppercase tracking-widest mt-1">Days</div></div>
                <div class="text-center"><div class="text-3xl sm:text-4xl md:text-5xl font-playfair text-secondary drop-shadow-lg" x-text="hours">00</div><div class="text-white/50 text-[10px] sm:text-xs uppercase tracking-widest mt-1">Hours</div></div>
                <div class="text-center"><div class="text-3xl sm:text-4xl md:text-5xl font-playfair text-secondary drop-shadow-lg" x-text="minutes">00</div><div class="text-white/50 text-[10px] sm:text-xs uppercase tracking-widest mt-1">Minutes</div></div>
                <div class="text-center"><div class="text-3xl sm:text-4xl md:text-5xl font-playfair text-secondary drop-shadow-lg" x-text="seconds">00</div><div class="text-white/50 text-[10px] sm:text-xs uppercase tracking-widest mt-1">Seconds</div></div>
            </div>
        @else
            <p class="mt-6 text-secondary font-playfair text-xl sm:text-2xl">Today is the day! <i class="bi bi-heart-fill"></i></p>
        @endif

        <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center items-center">
            <a href="#rsvp" class="inline-block bg-secondary hover:bg-secondary/90 text-primary font-semibold px-8 py-3 rounded-full text-xs sm:text-sm uppercase tracking-wider transition-all duration-300 shadow-lg shadow-secondary/20 hover:shadow-secondary/40 hover:scale-105">RSVP Now</a>
            <button type="button" id="open-invitation" class="inline-block border-2 border-secondary/50 hover:border-secondary text-secondary font-semibold px-8 py-3 rounded-full text-xs sm:text-sm uppercase tracking-wider transition-all duration-300 hover:bg-secondary/10 hover:scale-105 cursor-pointer"><i class="bi bi-envelope-paper-heart mr-1.5"></i>View Invitation</button>
            <a href="{{ route('gallery') }}" class="inline-block border-2 border-secondary/50 hover:border-secondary text-secondary font-semibold px-8 py-3 rounded-full text-xs sm:text-sm uppercase tracking-wider transition-all duration-300 hover:bg-secondary/10 hover:scale-105"><i class="bi bi-images mr-1.5"></i>Photo Album</a>
        </div>
    </div>

    {{-- Film-strip carousel: full-width snap, fits every screen --}}
    @php
        $heroSlides = $wedding->heroImages->isNotEmpty()
            ? $wedding->heroImages->pluck('image_path')->all()
            : array_filter([$wedding->hero_image_path]);
    @endphp
    @if(count($heroSlides))
        <div class="relative z-10 w-full mt-2 pb-10 sm:pb-14">
            <div class="flex items-center justify-center gap-3 mb-3 text-secondary/90">
                <div class="w-10 h-px bg-gradient-to-r from-transparent to-secondary/60"></div>
                <p class="tracking-[0.35em] uppercase text-[10px] sm:text-xs font-medium">Our Moments</p>
                <div class="w-10 h-px bg-gradient-to-l from-transparent to-secondary/60"></div>
            </div>
            <div id="album-strip" class="w-full">
                <div id="album-track" class="album-track" style="--slides: {{ count($heroSlides) }}">
                    @foreach($heroSlides as $slide)
                        <figure class="album-slide">
                            <img src="{{ asset('storage/' . $slide) }}" alt="A moment of {{ $wedding->bride_name }} and {{ $wedding->groom_name }}" loading="lazy" draggable="false">
                        </figure>
                    @endforeach
                </div>
                @if(count($heroSlides) > 1)
                    <div id="album-dots" class="flex justify-center gap-1.5 mt-3" aria-hidden="true"></div>
                @endif
            </div>
        </div>
    @endif

    {{-- Scroll teaser --}}
    <a href="#story" class="scroll-teaser" aria-label="Scroll to our story">
        <span>Scroll for our story</span>
        <i class="bi bi-chevron-double-down"></i>
    </a>
</section>

{{-- ═══════════════════════ OUR STORY ═══════════════════════ --}}
<section id="story" class="py-16 sm:py-24 bg-ivory relative overflow-hidden">
    <div class="absolute top-10 left-6 w-2 h-2 rounded-full bg-secondary/40" aria-hidden="true"></div>
    <div class="absolute bottom-16 right-10 w-1.5 h-1.5 rounded-full bg-secondary/30" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">How it all began</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-primary">Our Story</h2>
            <div class="ornament"><span></span><i class="bi bi-heart-fill"></i><span></span></div>
        </header>

        @forelse($milestones as $m)
            <article class="story-chapter reveal {{ $loop->even ? 'story-right' : '' }}">
                @if($m->photos->count())
                    <div class="story-photos" data-rotator {{ $m->photos->count() > 1 ? 'title="Tap to see the next photo"' : '' }}>
                        @foreach($m->photos as $i => $photo)
                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $m->title }} — photo {{ $i + 1 }}"
                                 loading="{{ $i === 0 ? 'eager' : 'lazy' }}" class="{{ $i === 0 ? 'is-active' : '' }}" draggable="false">
                        @endforeach
                        @if($m->photos->count() > 1)
                            <div class="story-photos-dots" aria-hidden="true">
                                @foreach($m->photos as $j => $p)<span class="sdot {{ $j === 0 ? 'on' : '' }}"></span>@endforeach
                            </div>
                            <span class="story-photos-next" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
                        @endif
                    </div>
                @endif
                <div class="story-body">
                    <span class="story-num font-playfair">{{ str_pad($m->display_order, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="font-playfair text-xl sm:text-2xl text-primary">{{ $m->title }}</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed mt-2">{{ $m->content }}</p>
                </div>
            </article>
        @empty
            <p class="text-center text-gray-400">Our story is being written…</p>
        @endforelse
    </div>
</section>

{{-- ═══════════════════════ SCHEDULE ═══════════════════════ --}}
<section id="schedule" class="py-16 sm:py-24 bg-primary relative overflow-hidden">
    <div class="petal-field" aria-hidden="true">
        @foreach (range(1, 16) as $i)
            <span class="petal" style="--i:{{ $i }};--x:{{ rand(3, 97) }}%;--t:{{ rand(3, 10) }}s;--r:{{ rand(0, 360) }}deg;--d:{{ rand(10, 20) }}s;">{{ ['✿', '❀', '❁'][rand(0, 2)] }}</span>
        @endforeach
    </div>
    <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>
    <div class="max-w-3xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">The order of the day</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-white">Schedule</h2>
            <div class="ornament"><span></span><i class="bi bi-clock-history"></i><span></span></div>
        </header>

        <ol class="day-timeline">
            @forelse($events as $e)
                <li class="day-item reveal">
                    <span class="day-dot"></span>
                    <time class="day-time font-playfair">{{ \Carbon\Carbon::parse($e->start_time)->format('g:i A') }}</time>
                    <div class="day-card">
                        <h3 class="font-semibold text-white">{{ $e->title }}</h3>
                        @if($e->end_time)
                            <p class="text-white/50 text-xs mt-0.5">until {{ \Carbon\Carbon::parse($e->end_time)->format('g:i A') }}</p>
                        @endif
                        @if($e->description)<p class="text-white/70 text-sm mt-1.5">{{ $e->description }}</p>@endif
                        @if($e->location)<p class="text-secondary/90 text-xs mt-1.5"><i class="bi bi-geo-alt-fill mr-1"></i>{{ $e->location }}</p>@endif
                    </div>
                </li>
            @empty
                <li class="text-center text-white/40 py-6">Schedule coming soon.</li>
            @endforelse
        </ol>
    </div>
</section>

{{-- ═══════════════════════ LOCATION ═══════════════════════ --}}
<section id="location" class="py-16 sm:py-24 bg-ivory">
    <div class="max-w-5xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">Find your way</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-primary">Location</h2>
            <div class="ornament"><span></span><i class="bi bi-geo-alt-fill"></i><span></span></div>
        </header>

        <div class="grid md:grid-cols-2 gap-5 sm:gap-6">
            <div class="venue-card reveal">
                <h3 class="font-playfair text-xl sm:text-2xl text-primary">Ceremony</h3>
                <p class="text-gray-600 font-medium text-sm sm:text-base mt-1">{{ $wedding->ceremony_venue ?? 'Venue TBD' }}</p>
                @if($wedding->ceremony_address)<p class="text-gray-400 text-xs sm:text-sm mt-1">{{ $wedding->ceremony_address }}</p>@endif
                @if($wedding->wedding_time)
                    <span class="mt-3 inline-flex items-center gap-1.5 bg-secondary/10 text-secondary px-3 py-1 rounded-full text-xs font-medium"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($wedding->wedding_time)->format('g:i A') }}</span>
                @endif
                @if($wedding->ceremony_venue && $wedding->ceremony_venue !== 'Venue TBD')
                    <div class="map-wrap mt-4">
                        <iframe src="https://maps.google.com/maps?q={{ urlencode($wedding->ceremony_address ?? $wedding->ceremony_venue) }}&output=embed&z=15" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map — {{ $wedding->ceremony_venue }}"></iframe>
                        <a href="{{ $wedding->ceremony_map_url ?? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($wedding->ceremony_address ?? $wedding->ceremony_venue) }}" target="_blank" rel="noopener" class="map-open" title="Open in Google Maps"><i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                @endif
            </div>

            <div class="venue-card reveal" style="--d:.12s">
                <h3 class="font-playfair text-xl sm:text-2xl text-primary">Reception</h3>
                <p class="text-gray-600 font-medium text-sm sm:text-base mt-1">{{ $wedding->reception_venue ?? 'Venue TBD' }}</p>
                @if($wedding->reception_address)<p class="text-gray-400 text-xs sm:text-sm mt-1">{{ $wedding->reception_address }}</p>@endif
                <div class="map-wrap mt-4">
                    @if($wedding->reception_venue && $wedding->reception_venue !== 'Venue TBD')
                        <iframe src="https://maps.google.com/maps?q={{ urlencode($wedding->reception_address ?? $wedding->reception_venue) }}&output=embed&z=15" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map — {{ $wedding->reception_venue }}"></iframe>
                        <a href="{{ $wedding->reception_map_url ?? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($wedding->reception_address ?? $wedding->reception_venue) }}" target="_blank" rel="noopener" class="map-open" title="Open in Google Maps"><i class="bi bi-box-arrow-up-right"></i></a>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-300 text-4xl"><i class="bi bi-geo-alt"></i></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Church & Pastor blessing --}}
        @if($wedding->church && ($wedding->church->name || $wedding->church->pastor_name || $wedding->church->message_from_church))
            <div class="mt-10 bg-white rounded-3xl border border-secondary/10 shadow-sm p-6 sm:p-8 text-center reveal" style="--d:.2s">
                <p class="eyebrow">With the support of</p>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary mt-2">{{ $wedding->church->name }}</h3>
                @if($wedding->church->pastor_name)
                    <p class="text-secondary font-medium mt-1 text-sm sm:text-base"><i class="bi bi-person-badge mr-1"></i>{{ $wedding->church->pastor_name }}</p>
                @endif
                @if($wedding->church->message_from_church)
                    <p class="mt-4 text-gray-600 italic max-w-2xl mx-auto leading-relaxed text-sm sm:text-base">“{{ $wedding->church->message_from_church }}”</p>
                @endif
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════ GOOD TO KNOW ═══════════════════════ --}}
@if($infoItems->count())
<section id="information" class="py-16 sm:py-24 bg-white">
    <div class="max-w-5xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">Everything you need</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-primary">Good to Know</h2>
            <div class="ornament"><span></span><i class="bi bi-card-checklist"></i><span></span></div>
        </header>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($infoItems as $item)
                <div class="info-card reveal" style="--d: {{ ($loop->index % 3) * 0.1 }}s">
                    <span class="info-emoji">{{ $item->icon }}</span>
                    <div>
                        <h3 class="font-semibold text-primary text-sm sm:text-base">{{ $item->title }}</h3>
                        <p class="text-gray-500 text-xs sm:text-sm mt-1 leading-relaxed">{{ $item->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════ RSVP ═══════════════════════ --}}
<section id="rsvp" class="py-16 sm:py-24 bg-ivory relative overflow-hidden">
    <div class="petal-field" aria-hidden="true">
        @foreach (range(1, 14) as $i)
            <span class="petal petal-gold" style="--i:{{ $i }};--x:{{ rand(3, 97) }}%;--t:{{ rand(3, 10) }}s;--r:{{ rand(0, 360) }}deg;--d:{{ rand(11, 21) }}s;">{{ ['✿', '❀', '✾'][rand(0, 2)] }}</span>
        @endforeach
    </div>
    <div class="absolute -top-28 -right-28 w-96 h-96 rounded-full bg-secondary/15 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-36 -left-28 w-[28rem] h-[28rem] rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>

    <div class="max-w-2xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">Kindly respond by {{ $wedding->rsvp_deadline ?? $wedding->wedding_date?->format('F j, Y') }}</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-primary">RSVP</h2>
            <div class="ornament"><span></span><i class="bi bi-envelope-heart-fill"></i><span></span></div>
        </header>

        @php
            /* If validation bounced the guest back, resume the wizard at the step
               where the problem is, with everything they typed still filled in. */
            $resumeStep = 1;
            if ($errors->any()) {
                $bad = $errors->keys();
                if (array_intersect(['message'], $bad)) $resumeStep = 5;
                elseif (array_intersect(['attendance', 'guest_count'], $bad)) $resumeStep = 4;
                elseif (in_array('supporting', $bad)) $resumeStep = 3;
                elseif (array_intersect(['relationship', 'relationship_other'], $bad)) $resumeStep = 2;
            }
        @endphp

        @include('pages.partials.rsvp-wizard')
    </div>
</section>

{{-- ═══════════════════════ WISHES ═══════════════════════ --}}
<section id="guestbook" class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-5 sm:px-6">
        <header class="section-head reveal">
            <p class="eyebrow">Words from the heart</p>
            <h2 class="font-playfair text-3xl sm:text-4xl md:text-5xl text-primary">Well Wishes</h2>
            <div class="ornament"><span></span><i class="bi bi-chat-heart-fill"></i><span></span></div>
        </header>

        @if($wishes->count())
            <div class="columns-1 sm:columns-2 gap-4 [column-fill:_balance] mb-10">
                @foreach($wishes as $w)
                    <figure class="wish-card reveal" style="--d: {{ ($loop->index % 4) * 0.08 }}s">
                        <blockquote class="text-gray-600 text-sm leading-relaxed">{{ $w->message }}</blockquote>
                        <figcaption class="mt-3 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-secondary/15 text-secondary flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($w->name, 0, 1)) }}</span>
                            <span class="text-primary font-medium text-sm">{{ $w->name }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif

        <div class="bg-ivory rounded-3xl border border-secondary/15 p-6 sm:p-8 reveal">
            <h3 class="font-playfair text-xl sm:text-2xl text-primary text-center">Leave a wish for the couple</h3>
            <p class="text-gray-400 text-xs text-center mt-1">Your kind words mean the world to them</p>
            <form action="{{ route('guestbook.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <input type="text" name="name" required placeholder="Your name"
                       class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 bg-white focus:border-secondary focus:ring-4 focus:ring-secondary/10 outline-none transition text-gray-700 placeholder:text-gray-300">
                <textarea name="message" required rows="3" placeholder="Write something sweet — we'll read every word…"
                          class="w-full px-5 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:border-secondary focus:ring-4 focus:ring-secondary/10 outline-none transition resize-none text-gray-700 placeholder:text-gray-300"></textarea>
                <button type="submit" class="w-full py-3.5 rounded-full bg-gradient-to-r from-secondary to-secondary/85 text-primary font-bold text-sm uppercase tracking-[0.2em] shadow-lg shadow-secondary/25 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all inline-flex items-center justify-center gap-2">
                    Send your wish <i class="bi bi-heart-fill"></i>
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════ FOOTER ═══════════════════════ --}}
<footer class="bg-primary text-white/70 py-12">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="font-playfair text-secondary text-xl">{{ $wedding->bride_name ?? '' }} & {{ $wedding->groom_name ?? '' }}</p>
        <p class="mt-2 text-sm">{{ $wedding->wedding_date?->format('F j, Y') ?? '' }}</p>
        @if($wedding->contact_email || $wedding->contact_phone)
            <p class="mt-3 text-xs text-white/50">
                <span class="text-white/35">Organizers:</span>
                @if($wedding->contact_email)<a href="mailto:{{ $wedding->contact_email }}" class="mr-3 hover:text-secondary transition-colors"><i class="bi bi-envelope mr-1"></i>{{ $wedding->contact_email }}</a>@endif
                @if($wedding->contact_phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $wedding->contact_phone) }}" class="hover:text-secondary transition-colors"><i class="bi bi-telephone mr-1"></i>{{ $wedding->contact_phone }}</a>@endif
            </p>
        @endif
        <p class="mt-5 text-xs text-white/40">Made with <i class="bi bi-heart-fill text-secondary"></i> for a beautiful celebration</p>
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" title="Admin"
           aria-label="Admin area"
           class="inline-flex items-center gap-1.5 mt-3 text-[11px] text-white/30 hover:text-secondary transition-colors">
            <i class="bi bi-gear"></i> Admin
        </a>
    </div>
</footer>

{{-- Success modals --}}
@if(session('rsvp_success'))
    <div id="flash-modal" class="flash-overlay" data-autoshow>
        <div class="flash-card">
            <div class="flash-heart"><i class="bi bi-balloon-heart-fill"></i></div>
            <h3 class="font-playfair text-2xl text-primary">Thank you, {{ session('rsvp_success') }}!</h3>
            <p class="text-gray-500 mt-2 text-sm">Your RSVP has been received. We can't wait to celebrate with you!</p>
            <button type="button" onclick="this.closest('#flash-modal').remove()" class="flash-close">Lovely!</button>
        </div>
    </div>
@endif
@if(session('wish_success'))
    <div id="flash-modal" class="flash-overlay" data-autoshow>
        <div class="flash-card">
            <div class="flash-heart"><i class="bi bi-chat-heart-fill"></i></div>
            <h3 class="font-playfair text-2xl text-primary">Thank you, {{ session('wish_success') }}!</h3>
            <p class="text-gray-500 mt-2 text-sm">Your wish has been sent to the couple — it will appear once approved.</p>
            <button type="button" onclick="this.closest('#flash-modal').remove()" class="flash-close">Sweet!</button>
        </div>
    </div>
@endif

{{-- ═══ Invitation modal — the paper invite, openable anytime from the hero ═══ --}}
<div id="invite-modal" class="invite-modal" style="display:none;" aria-hidden="true">
    <div class="invite-modal-inner">
        <div id="invitation-card" class="invite-card" data-names="{{ $wedding->full_title ?? 'wedding' }}" data-date="{{ $wedding->wedding_date?->format('F j, Y') ?? '' }}">
            @if($wedding->logo_path)
                <img src="{{ asset('storage/' . $wedding->logo_path) }}" alt="Wedding logo" class="ic-logo">
            @elseif($wedding->bride_name && $wedding->groom_name)
                <div class="ic-monogram font-playfair" aria-hidden="true">
                    {{ mb_substr($wedding->bride_name, 0, 1) }}<span>&amp;</span>{{ mb_substr($wedding->groom_name, 0, 1) }}
                </div>
            @endif
            <div class="ic-ornament"><span></span><i class="bi bi-heart-fill"></i><span></span></div>
            <p class="ic-eyebrow">The wedding celebration of</p>
            <h2 class="ic-names font-playfair">
                <span class="ic-name-line">{{ $wedding->bride_name ?? 'Bride' }}</span>
                <span class="ic-amp" aria-hidden="true">&amp;</span>
                <span class="ic-name-line">{{ $wedding->groom_name ?? 'Groom' }}</span>
            </h2>
            @if($wedding->couple_photo_path)
                <div class="ic-photo-frame">
                    <span class="ic-photo-badge" aria-hidden="true"><i class="bi bi-heart-fill"></i></span>
                    <img src="{{ asset('storage/' . $wedding->couple_photo_path) }}" alt="{{ $wedding->full_title ?? 'The couple' }}" class="ic-photo">
                </div>
            @endif
            @if($wedding->invitation_message)
                <p class="ic-message">{{ $wedding->invitation_message }}</p>
            @else
                <p class="ic-message">Together with their families, {{ $wedding->bride_name ?? 'they' }} &amp; {{ $wedding->groom_name ?? '' }} invite you to share in their joy as they exchange vows.</p>
            @endif
            <div class="ic-divider" aria-hidden="true"><span></span><i class="bi bi-flower1"></i><span></span></div>
            @if($wedding->wedding_date)
                <p class="ic-date font-playfair">
                    <span class="ic-dow">{{ $wedding->wedding_date->format('l') }}</span>
                    <span class="ic-dom">{{ $wedding->wedding_date->format('j') }}</span>
                    <span class="ic-moy">{{ $wedding->wedding_date->format('F Y') }}</span>
                </p>
            @else
                <p class="ic-date font-playfair">Date to be announced</p>
            @endif
            @if($wedding->wedding_time)
                <p class="ic-time">at {{ \Carbon\Carbon::parse($wedding->wedding_time)->format('g:i A') }}</p>
            @endif
            @if($wedding->ceremony_venue || $wedding->reception_venue)
                <div class="ic-places">
                    @if($wedding->ceremony_venue)
                        <div class="ic-place">
                            <p class="ic-place-label">The Ceremony</p>
                            <p class="ic-venue"><i class="bi bi-geo-alt-fill"></i> {{ $wedding->ceremony_venue }}</p>
                            @if($wedding->ceremony_address)<p class="ic-addr">{{ $wedding->ceremony_address }}</p>@endif
                        </div>
                    @endif
                    @if($wedding->reception_venue)
                        <div class="ic-place">
                            <p class="ic-place-label">Reception to follow</p>
                            <p class="ic-venue"><i class="bi bi-cup"></i> {{ $wedding->reception_venue }}</p>
                            @if($wedding->reception_address)<p class="ic-addr">{{ $wedding->reception_address }}</p>@endif
                        </div>
                    @endif
                </div>
            @endif
            @if($wedding->dress_code || $wedding->rsvp_deadline)
                <div class="ic-facts">
                    @if($wedding->dress_code)
                        <span class="ic-fact"><i class="bi bi-stars"></i> {{ $wedding->dress_code }}</span>
                    @endif
                    @if($wedding->rsvp_deadline)
                        <span class="ic-fact"><i class="bi bi-calendar-check"></i> RSVP by {{ \Carbon\Carbon::parse($wedding->rsvp_deadline)->format('j M Y') }}</span>
                    @endif
                </div>
            @endif
            @if($wedding->family_bride_info || $wedding->family_groom_info)
                <div class="ic-families">
                    @if($wedding->family_bride_info)<p>{{ $wedding->family_bride_info }}</p>@endif
                    @if($wedding->family_groom_info)<p>{{ $wedding->family_groom_info }}</p>@endif
                </div>
            @endif
            <a href="#rsvp" id="ic-rsvp" class="ic-rsvp">RSVP <i class="bi bi-arrow-right-short"></i></a>
            {{-- decorative corner flourishes (absolute, so they can sit anywhere in the flow) --}}
            <i class="ic-corner ic-tl" aria-hidden="true"></i>
            <i class="ic-corner ic-tr" aria-hidden="true"></i>
            <i class="ic-corner ic-bl" aria-hidden="true"></i>
            <i class="ic-corner ic-br" aria-hidden="true"></i>
            <div class="ic-ornament"><span></span><i class="bi bi-gem"></i><span></span></div>
        </div>
        <div class="invite-modal-actions">
            <button type="button" id="invite-modal-download" class="invite-btn invite-btn-gold"><i class="bi bi-file-earmark-pdf"></i> Save as PDF</button>
            <button type="button" id="invite-modal-close" class="invite-btn invite-btn-ghost">Close</button>
        </div>
    </div>
</div>

{{-- Desktop section dot-nav --}}
<nav id="dot-nav" class="hidden lg:flex fixed right-6 top-1/2 -translate-y-1/2 z-40 flex-col gap-3" aria-label="Sections">
    @foreach([['id'=>'hero','label'=>'Home'],['id'=>'story','label'=>'Our Story'],['id'=>'schedule','label'=>'Schedule'],['id'=>'location','label'=>'Location'],['id'=>'information','label'=>'Good to Know'],['id'=>'rsvp','label'=>'RSVP'],['id'=>'guestbook','label'=>'Well Wishes']] as $d)
        <a href="#{{ $d['id'] }}" data-navlink="{{ $d['id'] }}" title="{{ $d['label'] }}" class="dot"></a>
    @endforeach
</nav>

@endsection

@push('styles')
<style>
    /* ═══ Anchored jumps shouldn't hide section tops under the fixed nav ═══ */
    section[id] { scroll-margin-top: 70px; }
    @media (max-width: 767px) { section[id] { scroll-margin-top: 14px; } }

    /* ═══ Reveal-on-scroll ═══ */
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity .7s ease var(--d, 0s), transform .7s cubic-bezier(.2,.7,.3,1) var(--d, 0s); }
    .reveal.in-view { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } }

    /* ═══ Section headers ═══ */
    .eyebrow { color: rgba(201,168,76,.9); letter-spacing: .3em; text-transform: uppercase; font-size: 11px; }
    #schedule .eyebrow { color: rgba(201,168,76,.85); }
    .section-head { text-align: center; margin-bottom: 3rem; }
    .ornament { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 18px; }
    .ornament span { width: 56px; height: 1px; background: linear-gradient(to right, transparent, rgba(201,168,76,.7)); }
    .ornament span:last-child { background: linear-gradient(to left, transparent, rgba(201,168,76,.7)); }
    .ornament i { color: #c9a84c; font-size: 13px; }

    /* delicate line-art leaf sprigs — quietly botanical corners on cards */
    .venue-card, .wish-card, .info-card { position: relative; }
    .venue-card::before, .venue-card::after,
    .wish-card::before, .wish-card::after,
    .info-card::before, .info-card::after {
        content: ''; position: absolute; width: 26px; height: 26px; opacity: .5; pointer-events: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c9a84c' stroke-width='1.4' stroke-linecap='round'><path d='M4 20 C 9 19 14 14 15 5'/><path d='M15 9 c 3 0 5 2 5 5 c -3 0 -5 -2 -5 -5 Z'/><path d='M11 13 c -3 0 -5 2 -5 5 c 3 0 5 -2 5 -5 Z'/></svg>");
        background-repeat: no-repeat;
    }
    .venue-card::before, .wish-card::before, .info-card::before { top: 10px; left: 10px; }
    .venue-card::after, .wish-card::after, .info-card::after { bottom: 10px; right: 10px; transform: rotate(180deg); }

    /* ═══ Wedding florals — shared drifting-petal field (.petal-field) ═══ */
    .petal-field { position: absolute; inset: 0; pointer-events: none; overflow: hidden; z-index: 0; }
    .petal-field .petal {
        position: absolute;
        top: -10vh;
        left: var(--x, 60%);
        font-size: clamp(1.1rem, 2.2vw, 2rem);
        color: #fdf8f0;
        text-shadow:
            0 0 6px rgba(253, 248, 240, .5),
            0 0 18px rgba(253, 248, 240, .2),
            0 0 34px rgba(201, 168, 76, .12);
        animation:
            driftDown var(--d, 9s) ease-in infinite,
            floatHue var(--t, 9s) linear infinite;
        animation-delay: calc(var(--i, 0) * -0.7s), 0s;
        opacity: 0;
        user-select: none;
        pointer-events: none;
    }
    .petal-field .petal:nth-child(3n) { color: #f0e0d0; text-shadow: 0 0 4px rgba(240, 224, 208, .45); }
    .petal-field .petal:nth-child(5n) { color: #ffffff; text-shadow: 0 0 8px rgba(255, 255, 255, .6); }
    /* soft gold petals for light sections — white would vanish on ivory */
    .petal-field .petal.petal-gold { color: rgba(201, 168, 76, .38); text-shadow: 0 0 6px rgba(201, 168, 76, .18); }
    @keyframes driftDown {
        0%   { opacity: 0; transform: translateY(-20px) rotate(var(--r, 0deg)) scale(0.6); }
        12%  { opacity: 1; }
        100% { opacity: 0; transform: translateY(118vh) rotate(calc(var(--r, 0deg) + 120deg)) scale(1.35); }
    }
    @keyframes floatHue {
        0%, 100% { filter: hue-rotate(0deg); opacity: 1; }
        50%      { filter: hue-rotate(7deg); opacity: .92; }
    }
    @media (prefers-reduced-motion: reduce) {
        .petal-field { display: none; }
    }

    /* ═══ Scroll teaser ═══ */
    .scroll-teaser { position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); z-index: 10; color: rgba(255,255,255,.55); font-size: 10px; letter-spacing: .25em; text-transform: uppercase; display: flex; flex-direction: column; align-items: center; gap: 2px; animation: teaserBob 2.2s ease-in-out infinite; }
    .scroll-teaser i { font-size: 14px; color: #c9a84c; }
    @keyframes teaserBob { 0%,100% { transform: translateX(-50%) translateY(0); } 50% { transform: translateX(-50%) translateY(6px); } }

    /* ═══ Album carousel: full-bleed snap, fits every screen ═══ */
    .album-track {
        display: flex; gap: 12px;
        overflow-x: auto;
        padding: 6px max(24px, calc(50vw - var(--slide-w) / 2)) 10px;
        scrollbar-width: none; -webkit-overflow-scrolling: touch;
    }
    .album-track::-webkit-scrollbar { display: none; }
    .album-slide {
        flex: 0 0 var(--slide-w);
        aspect-ratio: 4 / 3;
        border-radius: 20px; overflow: hidden; margin: 0;
        box-shadow: 0 14px 34px rgba(0,0,0,.4), 0 0 0 1px rgba(201,168,76,.25);
        background: #0f2a1d;
    }
    .album-slide img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 25%; display: block; user-select: none; }
    :root { --slide-w: min(86vw, 560px); }
    @media (min-width: 768px) { :root { --slide-w: min(52vw, 640px); } }
    .album-dot { width: 6px; height: 6px; border-radius: 999px; background: rgba(255,255,255,.25); transition: all .35s ease; cursor: pointer; }
    .album-dot.on { width: 18px; background: #c9a84c; }

    /* ═══ Story chapters ═══ */
    .story-chapter { position: relative; display: grid; gap: 18px; padding: 22px 0; }
    @media (min-width: 768px) {
        .story-chapter { grid-template-columns: 380px 1fr; align-items: center; }
        .story-chapter.story-right { grid-template-columns: 1fr 380px; }
        .story-chapter.story-right .story-photos { order: 2; }
    }
    .story-photos { position: relative; aspect-ratio: 4/3; border-radius: 18px; overflow: hidden; box-shadow: 0 16px 36px rgba(26,60,42,.16), 0 0 0 1px rgba(201,168,76,.18); cursor: pointer; }
    .story-photos img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 50% 22%; opacity: 0; transition: opacity 1s ease; pointer-events: none; }
    .story-photos img.is-active { opacity: 1; }
    .story-photos-dots { position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px; z-index: 3; padding: 5px 9px; border-radius: 999px; background: rgba(13,31,21,.35); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); }
    .sdot { width: 6px; height: 6px; border-radius: 999px; background: rgba(255,255,255,.45); transition: all .3s ease; }
    .sdot.on { width: 16px; background: #c9a84c; }
    .story-photos-next { position: absolute; right: 10px; top: 10px; z-index: 3; width: 30px; height: 30px; border-radius: 50%; background: rgba(13,31,21,.4); color: #f3d98b; display: flex; align-items: center; justify-content: center; font-size: 16px; opacity: 0; transition: opacity .3s ease; -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); }
    .story-photos:hover .story-photos-next { opacity: 1; }
    @media (prefers-reduced-motion: reduce) { .story-photos img { transition: none; } }
    .story-num { display: inline-block; color: #c9a84c; font-size: 13px; letter-spacing: .2em; margin-bottom: 4px; }
    .story-body h3 { margin-top: 2px; }
    .story-chapter + .story-chapter { border-top: 1px dashed rgba(201,168,76,.35); }

    /* ═══ Schedule timeline ═══ */
    .day-timeline { position: relative; padding-left: 26px; }
    .day-timeline::before { content: ''; position: absolute; left: 7px; top: 8px; bottom: 8px; width: 2px; background: linear-gradient(rgba(201,168,76,.65), rgba(201,168,76,.08)); }
    .day-item { position: relative; display: grid; grid-template-columns: 74px 1fr; gap: 14px; align-items: start; padding: 12px 0; }
    .day-dot { position: absolute; left: -26px; top: 20px; width: 16px; height: 16px; border-radius: 50%; background: #c9a84c; box-shadow: 0 0 0 4px rgba(201,168,76,.18); }
    .day-time { color: #c9a84c; font-size: 13px; padding-top: 16px; white-space: nowrap; }
    .day-card { background: rgba(255,255,255,.05); border: 1px solid rgba(201,168,76,.18); border-radius: 16px; padding: 14px 18px; transition: transform .35s ease, border-color .35s ease; }
    .day-card:hover { transform: translateX(4px); border-color: rgba(201,168,76,.4); }

    /* ═══ Venue cards ═══ */
    .venue-card { background: #fff; border-radius: 24px; padding: 22px 22px 20px; box-shadow: 0 10px 30px rgba(26,60,42,.07); border: 1px solid rgba(201,168,76,.12); }
    .map-wrap { position: relative; height: 150px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(201,168,76,.15); box-shadow: inset 0 0 0 1px rgba(0,0,0,.02); }
    .map-open { position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,.92); color: #1a3c2a; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,.18); transition: all .3s ease; }
    .map-open:hover { background: #c9a84c; color: #1a3c2a; }

    /* ═══ Info cards ═══ */
    .info-card { display: flex; gap: 14px; align-items: flex-start; background: #fdf8f0; border: 1px solid rgba(201,168,76,.14); border-radius: 18px; padding: 16px 18px; transition: transform .3s ease, box-shadow .3s ease; }
    .info-card:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(26,60,42,.08); }
    .info-emoji { font-size: 24px; line-height: 1; }

    /* ═══ Wish cards ═══ */
    .wish-card { break-inside: avoid; background: #fdf8f0; border: 1px solid rgba(201,168,76,.14); border-radius: 18px; padding: 18px 20px; margin-bottom: 16px; box-shadow: 0 6px 18px rgba(26,60,42,.05); }

    /* ═══ Dot nav ═══ */
    .dot { width: 9px; height: 9px; border-radius: 50%; background: rgba(201,168,76,.35); transition: all .3s ease; position: relative; }
    .dot:hover { background: rgba(201,168,76,.8); transform: scale(1.3); }
    .dot.on { background: #c9a84c; transform: scale(1.35); box-shadow: 0 0 0 4px rgba(201,168,76,.18); }

    /* ═══ Success modal ═══ */
    .flash-overlay { position: fixed; inset: 0; z-index: 9990; background: rgba(13,31,21,.55); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 24px; opacity: 0; animation: fadeIn .45s ease forwards; }
    .flash-card { background: #fffdf6; border-radius: 26px; padding: 34px 30px 28px; max-width: 380px; width: 100%; text-align: center; box-shadow: 0 30px 80px rgba(0,0,0,.4), 0 0 0 1px rgba(201,168,76,.35); transform: scale(.9); animation: cardIn .5s cubic-bezier(.2,.9,.3,1.2) .1s forwards; }
    .flash-heart { width: 74px; height: 74px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(160deg, rgba(201,168,76,.25), rgba(201,168,76,.08)); color: #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 34px; }
    .flash-close { margin-top: 18px; background: #1a3c2a; color: #fff; font-size: 13px; font-weight: 600; letter-spacing: .08em; padding: 11px 30px; border-radius: 999px; transition: all .3s ease; }
    .flash-close:hover { background: #245238; transform: translateY(-1px); }
    @keyframes fadeIn { to { opacity: 1; } }
    @keyframes cardIn { to { transform: scale(1); } }

    /* ═══ Stars (kept from before) ═══ */
    .star { position: absolute; background: white; border-radius: 50%; animation: twinkle var(--duration) ease-in-out infinite alternate; animation-delay: var(--delay); }
    .star-gold { background: #c9a84c; box-shadow: 0 0 6px #c9a84c80; }
    @keyframes twinkle { 0% { opacity: var(--min-opacity); transform: scale(.8); } 100% { opacity: var(--max-opacity); transform: scale(1.2); } }
    .shooting-star { position: absolute; width: 2px; height: 2px; background: white; border-radius: 50%; animation: shoot 4s linear infinite; opacity: 0; }
    .shooting-star::after { content: ''; position: absolute; width: 60px; height: 1px; background: linear-gradient(to right, white, transparent); top: 0; right: 0; }
    @keyframes shoot { 0% { transform: translate(0,0); opacity: 0; } 5% { opacity: 1; } 30% { opacity: 0; transform: translate(-200px,120px); } 100% { opacity: 0; transform: translate(-200px,120px); } }

    /* ═══ RSVP confetti ═══ */
    @keyframes confettiFly { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(-200px) rotate(720deg); opacity: 0; } }
    .confetti-particle { position: fixed; width: 10px; height: 10px; border-radius: 2px; animation: confettiFly 1s ease-out forwards; pointer-events: none; z-index: 9999; }

    /* ═══ Opening intro ═══ */
    #wedding-intro { position: fixed; inset: 0; z-index: 9997; display: flex; align-items: center; justify-content: center; text-align: center; opacity: 0; pointer-events: none; overflow: hidden; transition: opacity .6s ease; background: radial-gradient(900px 480px at 85% -10%, rgba(201,168,76,.14), transparent 60%), radial-gradient(800px 460px at -12% 110%, rgba(201,168,76,.10), transparent 55%), linear-gradient(170deg, var(--wbg, #1a3c2a) 0%, #0b1f14 130%); }
    #wedding-intro.is-on { opacity: 1; pointer-events: auto; }
    #wedding-intro.is-done { opacity: 0; pointer-events: none; }
    .intro-stage { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity .55s ease, transform .55s ease; padding: 20px; }
    #intro-envelope-stage { opacity: 0; transform: translateY(18px) scale(.97); }
    #wedding-intro.env-stage-on #intro-envelope-stage { opacity: 1; transform: none; }
    #intro-photo-stage { opacity: 0; transform: scale(.94); pointer-events: none; }
    #wedding-intro.photo-stage-on #intro-photo-stage { opacity: 1; transform: none; pointer-events: auto; }
    #wedding-intro.photo-stage-on #intro-envelope-stage { opacity: 0; transform: translateY(-26px) scale(.94); pointer-events: none; }
    .intro-eyebrow { color: rgba(255,255,255,.55); letter-spacing: .34em; text-transform: uppercase; font-size: 11px; margin-bottom: 26px; }

    /* Envelope stage: names + date under the invitation eyebrow */
    .env-names { color: #fff; font-size: clamp(24px, 6.4vw, 40px); font-weight: 500; line-height: 1.25; margin-top: -12px; text-shadow: 0 4px 26px rgba(0,0,0,.4); }
    .env-names em { color: var(--wgold, #c9a84c); font-style: italic; font-weight: 400; margin: 0 .18em; }
    .env-date { color: var(--wgold, #c9a84c); font-size: 13px; letter-spacing: .22em; text-transform: uppercase; margin: 8px 0 30px; }
    /* drifting glow-sparkles behind the envelope */
    #intro-env-floaties { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
    .env-floatie { position: absolute; bottom: -28px; left: var(--x); font-style: normal; font-size: var(--fs); color: rgba(243,217,139,.85); opacity: 0; text-shadow: 0 0 10px rgba(201,168,76,.7); animation: envFloat var(--dur) linear var(--delay) infinite; }
    @keyframes envFloat { 0% { transform: translateY(0) rotate(0deg); opacity: 0; } 12% { opacity: var(--op); } 85% { opacity: var(--op); } 100% { transform: translateY(-108vh) rotate(var(--rot)); opacity: 0; } }

    /* white petals drifting down through the whole invitation moment */
    #intro-petals { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
    .intro-petal {
        position: absolute; top: -6vh; left: var(--ix, 50%); font-style: normal;
        font-size: clamp(14px, 2vw, 22px); color: rgba(255,255,255,.92);
        text-shadow: 0 0 8px rgba(255,255,255,.45), 0 0 20px rgba(243,217,139,.35);
        animation: introPetalFall var(--id, 10s) linear var(--idl, 0s) infinite;
        opacity: 0;
    }
    .intro-petal:nth-child(3n) { color: rgba(243,217,139,.9); }
    .intro-petal:nth-child(5n) { color: #fff; font-size: clamp(10px, 1.4vw, 15px); }
    @keyframes introPetalFall {
        0% { opacity: 0; transform: translateY(-6vh) translateX(0) rotate(var(--ir, 0deg)); }
        10% { opacity: .9; }
        50% { transform: translateY(52vh) translateX(24px) rotate(calc(var(--ir, 0deg) + 150deg)); }
        90% { opacity: .8; }
        100% { opacity: 0; transform: translateY(112vh) translateX(-18px) rotate(calc(var(--ir, 0deg) + 320deg)); }
    }
    @media (max-width: 480px) { .env-names { font-size: clamp(21px, 7.2vw, 30px); } .env-date { margin: 7px 0 22px; } }

    /* ═══ Invitation card — opened anytime from the hero button ═══ */
    .invite-card {
        position: relative;
        width: min(340px, 86vw);
        aspect-ratio: 5 / 7; /* traditional paper-invite portrait */
        max-height: 70vh;
        overflow-y: auto;
        overscroll-behavior: contain;
        display: flex;
        flex-direction: column;
        align-items: center;
        background:
            radial-gradient(circle at 50% -8%, rgba(243,217,139,.4), transparent 46%),
            radial-gradient(circle at 112% 108%, rgba(201,168,76,.16), transparent 42%),
            repeating-linear-gradient(45deg, rgba(201,168,76,.03) 0 2px, transparent 2px 7px),
            linear-gradient(160deg, #fffdf6, #f7efdb);
        border: 1px solid rgba(201,168,76,.45);
        border-radius: 12px;
        padding: 34px 26px;
        text-align: center;
        box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(201,168,76,.25), 0 0 44px rgba(201,168,76,.18);
        -webkit-overflow-scrolling: touch;
    }
    /* ═══ Invitation card scale: generous on desktop, content-hugging on phones ═══ */
    @media (min-width: 768px) {
        .invite-card { width: min(420px, 86vw); padding: 44px 36px 40px; max-height: 80vh; }
        .ic-logo { max-width: 150px; max-height: 66px; }
        .ic-photo { width: min(230px, 52vw); }
        .ic-photo-frame { margin-top: 20px; }
        .ic-eyebrow { font-size: 10px; margin-top: 16px; }
        .ic-name-line { font-size: clamp(30px, 3.2vw, 38px); }
        .ic-amp { width: 38px; height: 38px; font-size: 21px; margin: 9px 0 6px; }
        .ic-message { font-size: 13.5px; }
        .ic-divider { margin: 22px auto; }
        .ic-divider span { width: 56px; }
        .ic-dom { font-size: 50px; }
        .ic-moy { font-size: 16px; }
        .ic-time { font-size: 12.5px; }
        .ic-venue { font-size: 14px; }
        .ic-families p { font-size: 12.5px; }
        .ic-monogram { width: 64px; height: 64px; font-size: 24px; }
        .ic-place-label { font-size: 10px; }
        .ic-addr { font-size: 12.5px; }
        .ic-fact { font-size: 10.5px; }
        .ic-rsvp { font-size: 11.5px; padding: 11px 26px; }
        .ic-corner { width: 30px; height: 30px; }
    }
    @media (max-width: 640px) {
        /* phones: drop the fixed portrait ratio so content never gets squeezed into
           a tiny scroll box — the card hugs its content (modal scrolls if needed) */
        .invite-card { aspect-ratio: auto; max-height: min(78vh, 660px); padding: 28px 20px 24px; }
        .invite-card > :first-child { margin-top: 0; }
        .invite-card > div:last-of-type { margin-bottom: 0; }
        .ic-photo { width: min(170px, 50vw); }
        .ic-name-line { font-size: clamp(24px, 7.2vw, 30px); }
        .ic-amp { width: 30px; height: 30px; font-size: 17px; margin: 6px 0 4px; }
        .ic-message { font-size: 12px; }
        .ic-dom { font-size: 38px; }
        .ic-dom::before, .ic-dom::after { width: 18px; }
        .ic-moy { font-size: 13px; }
        .ic-rsvp { margin-top: 14px; }
        .ic-corner { width: 22px; height: 22px; }
    }
    /* auto margins centre the content on the tall card, and collapse safely when it scrolls
       (last in-flow block is the gem ornament — the corner flourishes are absolute) */
    .invite-card > :first-child { margin-top: auto; }
    .invite-card > div:last-of-type { margin-bottom: auto; }
    .invite-card::before { content: ''; position: absolute; inset: 9px; border: 1px solid rgba(201,168,76,.55); border-radius: 8px; pointer-events: none; }
    .ic-ornament { display: flex; align-items: center; justify-content: center; gap: 10px; color: #c9a84c; font-size: 11px; }
    .ic-ornament span { width: 40px; height: 1px; background: linear-gradient(to right, transparent, rgba(201,168,76,.8)); }
    .ic-ornament span:last-child { background: linear-gradient(to left, transparent, rgba(201,168,76,.8)); }
    .ic-logo { max-width: 130px; max-height: 58px; object-fit: contain; margin-top: 12px; }
    /* couple photo: layered paper frame with a wax-seal heart badge */
    .ic-photo-frame { position: relative; z-index: 0; margin-top: 16px; }
    .ic-photo-frame::before { content: ''; position: absolute; inset: -7px -7px -11px; background: #fff; border: 1px solid rgba(201,168,76,.5); border-radius: 12px; box-shadow: 0 10px 24px rgba(26,60,42,.16); transform: rotate(-1.6deg); }
    .ic-photo-frame::after { content: ''; position: absolute; inset: -4px -4px -6px; background: #fdf6e3; border: 1px solid rgba(201,168,76,.35); border-radius: 11px; transform: rotate(1.2deg); z-index: -1; }
    .ic-photo-badge { position: absolute; right: -9px; top: -9px; z-index: 3; width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(160deg, #f3d98b, #c9a84c); color: #10281b; display: flex; align-items: center; justify-content: center; font-size: 12px; box-shadow: 0 6px 14px rgba(201,168,76,.5), 0 0 0 3px #fffdf6; }
    .ic-photo {
        position: relative; z-index: 2;
        width: min(190px, 56vw);
        aspect-ratio: 4 / 5;
        object-fit: cover;
        object-position: 50% 25%;
        border-radius: 10px;
        border: 3px solid #fffdf6;
        box-shadow: 0 8px 20px rgba(26,60,42,.20), 0 0 0 1px rgba(201,168,76,.4);
        display: block;
    }
    .ic-eyebrow { margin-top: 14px; color: #9c937c; font-size: 9px; letter-spacing: .3em; text-transform: uppercase; }
    /* names: stacked lines with a gold circled ampersand between them */
    .ic-names { margin-top: 10px; color: #1a3c2a; display: flex; flex-direction: column; align-items: center; gap: 2px; }
    .ic-name-line { display: block; font-size: clamp(26px, 7vw, 34px); font-weight: 600; line-height: 1.15; }
    .ic-amp { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; margin: 7px 0 5px; border: 1.5px solid rgba(201,168,76,.8); border-radius: 50%; color: #c9a84c; font-style: italic; font-weight: 400; font-size: 19px; line-height: 1; background: radial-gradient(circle at 32% 26%, rgba(243,217,139,.35), transparent 65%); }

    /* flower divider */
    .ic-divider { display: flex; align-items: center; justify-content: center; gap: 10px; margin: 18px auto; }
    .ic-divider span { width: 46px; height: 1px; background: linear-gradient(to right, transparent, rgba(201,168,76,.85)); }
    .ic-divider span:last-child { background: linear-gradient(to left, transparent, rgba(201,168,76,.85)); }
    .ic-divider i { color: #c9a84c; font-size: 13px; transform: rotate(0deg); }

    /* date block: small caps day + big numeral + month/year */
    .ic-date { color: #1a3c2a; display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .ic-dow { font-size: 11px; letter-spacing: .34em; text-transform: uppercase; color: #8a7f63; }
    .ic-dom { position: relative; font-size: 44px; font-weight: 600; line-height: 1; padding: 0 6px; }
    .ic-dom::before, .ic-dom::after { content: ''; position: absolute; top: 50%; width: 26px; height: 1px; background: linear-gradient(to right, transparent, rgba(201,168,76,.7)); }
    .ic-dom::before { right: calc(100% + 8px); }
    .ic-dom::after { left: calc(100% + 8px); background: linear-gradient(to left, transparent, rgba(201,168,76,.7)); }
    .ic-moy { font-size: 15px; letter-spacing: .12em; text-transform: uppercase; color: #1a3c2a; font-weight: 500; }

    /* RSVP pill on the card itself */
    .ic-rsvp { margin-top: 18px; display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(160deg, #f3d98b, #c9a84c); color: #10281b; font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; padding: 10px 22px; border-radius: 999px; box-shadow: 0 8px 20px rgba(201,168,76,.4); transition: all .3s ease; -webkit-tap-highlight-color: transparent; }
    .ic-rsvp:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(201,168,76,.55); }
    .ic-rsvp i { font-size: 14px; transition: transform .3s ease; }
    .ic-rsvp:hover i { transform: translateX(3px); }

    /* gold corner flourishes */
    .ic-corner { position: absolute; width: 26px; height: 26px; border: 0 solid rgba(201,168,76,.75); pointer-events: none; z-index: 3; }
    .ic-tl { top: 9px; left: 9px; border-top-width: 1.5px; border-left-width: 1.5px; border-top-left-radius: 8px; }
    .ic-tr { top: 9px; right: 9px; border-top-width: 1.5px; border-right-width: 1.5px; border-top-right-radius: 8px; }
    .ic-bl { bottom: 9px; left: 9px; border-bottom-width: 1.5px; border-left-width: 1.5px; border-bottom-left-radius: 8px; }
    .ic-br { bottom: 9px; right: 9px; border-bottom-width: 1.5px; border-right-width: 1.5px; border-bottom-right-radius: 8px; }
    .ic-message { margin-top: 14px; color: #6d6552; font-size: 12.5px; line-height: 1.7; font-style: italic; }
    .ic-rule { width: 54px; height: 1px; background: #c9a84c; margin: 18px auto; opacity: .8; } /* kept for fallback (no-date cards) */
    .ic-time { margin-top: 4px; color: #6d6552; font-size: 12px; }
    .ic-venue { margin-top: 12px; color: #1a3c2a; font-size: 13px; font-weight: 500; }
    .ic-venue i { color: #c9a84c; margin-right: 4px; }
    .ic-families { margin-top: 16px; display: flex; flex-direction: column; gap: 6px; }
    .ic-families p { color: #6d6552; font-size: 11.5px; line-height: 1.6; }

    /* gold monogram when the couple has no logo uploaded */
    .ic-monogram {
        width: 56px; height: 56px; margin-top: 8px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 21px; color: #1a3c2a;
        background: radial-gradient(circle at 32% 26%, rgba(243,217,139,.5), rgba(201,168,76,.14));
        border: 1.5px solid rgba(201,168,76,.75);
        box-shadow: 0 6px 16px rgba(201,168,76,.28), inset 0 0 0 3px #fffdf6, inset 0 0 0 4px rgba(201,168,76,.4);
    }
    .ic-monogram span { color: #c9a84c; font-style: italic; margin: 0 .08em; }

    /* ceremony / reception blocks — hairline-caps label, venue, address */
    .ic-places { margin-top: 14px; display: flex; flex-direction: column; gap: 12px; width: 100%; align-items: center; }
    .ic-place { display: flex; flex-direction: column; align-items: center; gap: 2px; }
    .ic-place + .ic-place { padding-top: 12px; border-top: 1px dashed rgba(201,168,76,.45); width: 82%; }
    .ic-place-label {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 9.5px; letter-spacing: .3em; text-transform: uppercase; color: #9c937c;
    }
    .ic-place-label::before, .ic-place-label::after { content: ''; width: 26px; height: 1px; background: linear-gradient(to right, transparent, rgba(201,168,76,.75)); }
    .ic-place-label::after { background: linear-gradient(to left, transparent, rgba(201,168,76,.75)); }
    .ic-place .ic-venue { margin-top: 3px; }
    .ic-addr { color: #8a7f63; font-size: 11.5px; font-style: italic; }

    /* dress code / RSVP-by chips */
    .ic-facts { margin-top: 15px; display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
    .ic-fact {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
        color: #6d5f3c;
        background: rgba(243,217,139,.35);
        border: 1px solid rgba(201,168,76,.35);
        padding: 6px 13px; border-radius: 999px;
    }
    .ic-fact i { color: #c9a84c; font-size: 11px; }
    .ic-ornament:last-child { margin-top: 18px; }
    .invite-actions { margin-top: 22px; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
    .invite-btn {
        border: none; border-radius: 999px; padding: 12px 24px; cursor: pointer;
        font-size: 11.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 8px; transition: all .3s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .invite-btn-gold { background: linear-gradient(160deg, #f3d98b, #c9a84c); color: #10281b; box-shadow: 0 10px 26px rgba(201,168,76,.35); }
    .invite-btn-gold:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(201,168,76,.5); }
    .invite-btn-ghost { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.25); color: rgba(255,255,255,.85); }
    .invite-btn-ghost:hover { background: rgba(201,168,76,.15); border-color: rgba(201,168,76,.6); color: #fff; }
    .invite-hint { margin-top: 12px; color: rgba(255,255,255,.4); font-size: 10px; letter-spacing: .12em; }

    /* ═══ Invitation modal (hero button) ═══ */
    .invite-modal { position: fixed; inset: 0; z-index: 9995; background: rgba(13,31,21,.72); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; padding: 20px; overflow-y: auto; animation: fadeIn .35s ease; }
    @media (max-width: 640px) { .invite-modal { padding: 14px; align-items: flex-start; } }
    .invite-modal-inner { display: flex; flex-direction: column; align-items: center; gap: 16px; max-height: 100%; margin: auto; }
    .invite-modal-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }

    /* ═══ Envelope — the turtle spirograph IS the envelope ═══ */
    .env-wrap { position: relative; display: inline-block; }
    .env-glow { position: absolute; inset: -44px; border-radius: 48px; background: radial-gradient(closest-side, rgba(201,168,76,.34), transparent); animation: envGlow 2.6s ease-in-out infinite; pointer-events: none; }
    .env-glow::after { content: ''; position: absolute; inset: 14%; border-radius: 50%; background: radial-gradient(closest-side, rgba(243,217,139,.30), transparent 70%); animation: envGlow 2.6s ease-in-out .6s infinite reverse; }
    @keyframes envGlow { 0%,100% { opacity: .5; transform: scale(1); } 50% { opacity: 1; transform: scale(1.06); } }
    .env-spiro-envelope {
        position: relative; z-index: 1;
        width: min(520px, 88vw);
        aspect-ratio: 1;
        cursor: pointer; outline: none;
        -webkit-tap-highlight-color: transparent;
        transition: opacity .5s ease;
        animation: envBob 5.2s ease-in-out infinite, envIn .9s cubic-bezier(.2,.8,.3,1) both;
    }
    @keyframes envIn { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }
    /* gentle bob so it reads as "alive, tap me" */
    @keyframes envBob { 0%,100% { transform: translateY(0) rotate(-.4deg); } 50% { transform: translateY(-10px) rotate(.5deg); } }
    .env-spiro-envelope:hover { transform: scale(1.04); }
    /* gold light sweeping across the mandala — draws the eye while it draws itself */
    .env-spiro-envelope::after {
        content: ''; position: absolute; inset: 0; z-index: 2; pointer-events: none;
        background: linear-gradient(115deg, transparent 34%, rgba(255,246,214,.34) 47%, rgba(255,246,214,.08) 52%, transparent 64%);
        mix-blend-mode: screen;
        transform: translateX(-120%);
        animation: envSheen 4.6s ease-in-out 1.2s infinite;
    }
    @keyframes envSheen { 0% { transform: translateX(-120%); } 28%,100% { transform: translateX(120%); } }
    /* the tap: the mandala spins open in a burst of light */
    .env-spiro-envelope.opening { animation: spiroBurst .95s cubic-bezier(.3,.7,.3,1) forwards; pointer-events: none; }
    @keyframes spiroBurst {
        0% { transform: scale(1) rotate(0deg); opacity: 1; filter: brightness(1); }
        35% { opacity: 1; filter: brightness(1.5); }
        100% { transform: scale(1.4) rotate(52deg); opacity: 0; filter: brightness(2); }
    }
    .env-tap {
        margin-top: 30px; color: #f3d98b; font-size: 12.5px; letter-spacing: .26em; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 10px;
        padding: 13px 26px; border-radius: 999px;
        border: 1px solid rgba(201,168,76,.5); background: rgba(201,168,76,.10);
        -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
        box-shadow: 0 0 0 0 rgba(201,168,76,.45);
        animation: tapPulse 1.8s ease-in-out infinite, tapRing 2.4s ease-out infinite;
    }
    .env-tap i { font-size: 17px; }
    @keyframes tapRing { 0% { box-shadow: 0 0 0 0 rgba(201,168,76,.45); } 70% { box-shadow: 0 0 0 16px rgba(201,168,76,0); } 100% { box-shadow: 0 0 0 0 rgba(201,168,76,0); } }
    @keyframes tapPulse { 0%,100% { opacity: .55; } 50% { opacity: 1; } }

    /* ═══ Turtle-drawn spirograph — the envelope's living artwork ═══
       The classic 80-square/80-circle turtle pattern, rendered as SVG:
       each line draws itself on like a pen — a long, deliberate buildup —
       then the whole mandala keeps slowly turning. */
    .env-spiro {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none;
    }
    .env-spiro svg { width: 96%; height: 96%; overflow: visible; animation: spiroTurn 90s linear infinite; filter: drop-shadow(0 0 5px rgba(233,217,139,.4)); }
    @keyframes spiroTurn { to { transform: rotate(360deg); } }
    .env-spiro path, .env-spiro circle {
        fill: none; vector-effect: non-scaling-stroke; stroke-linecap: round;
        stroke: rgba(233,217,139,.9); stroke-width: 1.5;
        stroke-dasharray: 100; stroke-dashoffset: 100;
    }
    .env-spiro .spiro-ci circle { stroke: rgba(201,168,76,.75); stroke-width: 1.1; }
    /* the buildup: squares sweep on one by one, then the circles weave in */
    #wedding-intro.env-stage-on .env-spiro path {
        animation: spiroDraw .8s ease forwards; animation-delay: calc(.2s + var(--i) * .05s);
    }
    #wedding-intro.env-stage-on .env-spiro circle {
        animation: spiroDraw .6s ease forwards; animation-delay: calc(2.6s + var(--i) * .04s);
    }
    @keyframes spiroDraw { to { stroke-dashoffset: 0; } }

    /* ═══ Reveal photo frame ═══ */
    .scratch-frame { width: min(430px, 92vw); background: #fffdf6; border-radius: 18px; padding: 10px 10px 12px; box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(201,168,76,.35), 0 0 40px rgba(201,168,76,.15); }
    .scratch-imgwrap { position: relative; width: 100%; aspect-ratio: 4/3; border-radius: 12px; overflow: hidden; background: linear-gradient(160deg, #1a3c2a, #0b1f14); }
    .scratch-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .scratch-fallback { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; color: #c9a84c; font-size: 15px; }
    .scratch-fallback i { font-size: 44px; }
    .reveal-cover { position: absolute; inset: 0; z-index: 4; display: flex; align-items: center; justify-content: center; cursor: pointer; touch-action: manipulation; -webkit-tap-highlight-color: transparent; background: radial-gradient(circle at 32% 20%, rgba(255,255,255,.35), transparent 42%), linear-gradient(160deg, #eed9a0, #c9a84c 55%, #a27f2e); color: rgba(20,45,32,.9); transition: opacity .5s ease, transform .5s ease; overflow: hidden; }
    /* moving light sweep + tiny sparkles keep the cover alive and tappable */
    .reveal-cover::after {
        content: ''; position: absolute; inset: -30%; pointer-events: none;
        background: linear-gradient(115deg, transparent 36%, rgba(255,255,255,.45) 48%, transparent 60%);
        transform: translateX(-70%);
        animation: coverSheen 3.4s ease-in-out infinite;
    }
    @keyframes coverSheen { 0% { transform: translateX(-70%); } 55%,100% { transform: translateX(70%); } }
    .reveal-cover .reveal-cover-inner { animation: coverBreathe 2.6s ease-in-out infinite; }
    @keyframes coverBreathe { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    /* white petals raining down behind the gold — it feels like a gift */
    .cover-petals { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .cover-petals span { position: absolute; top: -12%; font-size: 15px; color: rgba(255,255,255,.9); text-shadow: 0 0 6px rgba(255,255,255,.5); animation: coverPetalFall var(--pd, 3.2s) linear var(--pdl, 0s) infinite; opacity: 0; }
    @keyframes coverPetalFall { 0% { opacity: 0; transform: translateY(-10%) rotate(0deg); } 12% { opacity: .95; } 100% { opacity: 0; transform: translateY(760%) rotate(240deg); } }
    .reveal-cover.open { opacity: 0; transform: scale(1.12); pointer-events: none; }
    .reveal-cover-inner { display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; padding: 18px; }
    .reveal-tap-icon { font-size: 34px; animation: tapPulse 1.8s ease-in-out infinite; }
    .reveal-cover-text { font-size: 22px; font-weight: 600; letter-spacing: .05em; }
    .reveal-cover-sub { font-size: 10px; letter-spacing: .3em; text-transform: uppercase; opacity: .8; }
    .scratch-img.pop { animation: imgPop .8s cubic-bezier(.2,.9,.3,1.15); }
    @keyframes imgPop { 0% { transform: scale(1); } 40% { transform: scale(1.14); } 100% { transform: scale(1); } }
    .reveal-flash { position: absolute; inset: 0; z-index: 5; pointer-events: none; opacity: 0; background: radial-gradient(circle at 50% 45%, rgba(255,246,220,.95), rgba(201,168,76,.4) 45%, transparent 72%); }
    .reveal-flash.go { animation: revealFlash .9s ease-out forwards; }
    @keyframes revealFlash { 0% { opacity: 0; } 25% { opacity: 1; } 100% { opacity: 0; } }
    .scratch-caption { padding-top: 10px; }
    .scratch-names { color: #1a3c2a; font-size: 19px; font-weight: 600; }

    /* ═══ Popup photos — clamped inside the screen ═══ */
    #intro-popups { position: absolute; inset: 0; z-index: 8; pointer-events: none; overflow: hidden; }
    .intro-popup { position: absolute; width: min(150px, 26vw); aspect-ratio: 4/5; border-radius: 14px; overflow: hidden; box-shadow: 0 26px 60px rgba(0,0,0,.5), 0 0 0 4px rgba(255,253,246,.95), 0 0 0 5px rgba(201,168,76,.45); opacity: 0; transform: translate(-50%,-50%) scale(.2) rotate(-10deg); animation: popupZoom 2.4s cubic-bezier(.2,.9,.3,1) forwards; }
    .intro-popup img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 22%; display: block; }
    @keyframes popupZoom { 0% { opacity: 0; transform: translate(-50%,-50%) scale(.15) rotate(-12deg); } 10% { opacity: 1; transform: translate(-50%,-50%) scale(1.08) rotate(2deg); } 20% { transform: translate(-50%,-50%) scale(1) rotate(0deg); } 82% { opacity: 1; transform: translate(-50%,-50%) scale(1.03); } 100% { opacity: 0; transform: translate(-50%,-50%) scale(1.55) rotate(9deg); } }

    /* ═══ Spark burst + skip ═══ */
    #intro-spark { position: absolute; left: 50%; top: 50%; width: 0; height: 0; z-index: 6; pointer-events: none; }
    .spark-bit { position: absolute; left: 0; top: 0; font-style: normal; color: #f3d98b; opacity: 0; animation: sparkFly .95s ease-out forwards; }
    .spark-bit.spark-heart { color: #fff; }
    .spark-bit.spark-petal { color: #fff; text-shadow: 0 0 8px rgba(255,255,255,.6), 0 0 16px rgba(243,217,139,.5); animation-duration: 1.4s; }
    @keyframes sparkFly { 0% { opacity: 0; transform: translate(-50%,-50%) scale(.4) rotate(0deg); } 15% { opacity: 1; } 100% { opacity: 0; transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy))) scale(var(--sc)) rotate(var(--spin, 0deg)); } }
    /* (skip button removed — the intro plays in full) */
    /* Replay pill — floats above the page after the intro, so guests can watch it again */
    .intro-replay { position: fixed; bottom: max(84px, calc(env(safe-area-inset-bottom, 0px) + 84px)); left: 50%; transform: translateX(-50%) translateY(8px); z-index: 60; display: inline-flex; align-items: center; gap: 7px; background: rgba(13,31,21,.72); border: 1px solid rgba(201,168,76,.45); color: rgba(255,255,255,.85); font-size: 10.5px; letter-spacing: .18em; text-transform: uppercase; padding: 8px 16px; border-radius: 999px; cursor: pointer; opacity: 0; pointer-events: none; transition: all .35s ease; -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); box-shadow: 0 10px 26px rgba(0,0,0,.3); }
    .intro-replay.show { opacity: 1; pointer-events: auto; transform: translateX(-50%) translateY(0); }
    .intro-replay i { color: var(--wgold, #c9a84c); }
    .intro-replay:hover { border-color: rgba(201,168,76,.9); color: #fff; background: rgba(201,168,76,.22); }
    .intro-replay[hidden] { display: none; }
    @media (min-width: 768px) {
        /* desktop has no bottom nav — park the pill bottom-right, clear of the scroll teaser */
        .intro-replay { bottom: 24px; left: auto; right: 24px; transform: translateY(8px); }
        .intro-replay.show { transform: translateY(0); }
    }

    /* ═══ Sound toggle — bottom-left, clear of the mobile bottom nav ═══ */
    .sound-toggle {
        position: fixed; left: 16px;
        bottom: max(84px, calc(env(safe-area-inset-bottom, 0px) + 84px));
        z-index: 60; width: 42px; height: 42px; border-radius: 50%;
        border: 1px solid rgba(201,168,76,.45);
        background: rgba(13,31,21,.72);
        color: var(--wgold, #c9a84c);
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; cursor: pointer;
        -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
        box-shadow: 0 10px 26px rgba(0,0,0,.3);
        transition: all .3s ease; -webkit-tap-highlight-color: transparent;
    }
    .sound-toggle:hover { border-color: rgba(201,168,76,.9); transform: scale(1.08); }
    .sound-toggle.is-off { color: rgba(255,255,255,.45); border-color: rgba(255,255,255,.22); }
    @media (min-width: 768px) { .sound-toggle { bottom: 24px; left: 24px; } }

    @media (prefers-reduced-motion: reduce) {
        .env-spiro-envelope, .intro-popup, .spark-bit, .reveal-tap-icon, .scroll-teaser, .env-glow,
        .intro-petal, .cover-petals, .env-tap, .reveal-cover::after, .env-spiro-envelope::after,
        .reveal-cover .reveal-cover-inner { animation: none !important; }
        #intro-petals, .cover-petals { display: none; }
        .env-spiro { animation: none !important; }
        .env-spiro path, .env-spiro circle { stroke-dashoffset: 0 !important; animation: none !important; }
        .intro-popup { opacity: .9; transform: translate(-50%,-50%) scale(1); }
        /* opening is animation-driven — fall back to a simple fade so it still disappears */
        .env-spiro-envelope.opening { opacity: 0 !important; transform: scale(1.22) translateY(-10px); }
    }
</style>
@endpush

@push('scripts')
<script>
/* ═══ Sounds — warm acoustic fingerpicking + soft chimes ═══
   The music is a live Karplus–Strong nylon-string guitar (no audio files,
   nothing to license): a slow G – D/F# – Em7 – Cadd9 picking pattern with
   the gentle sway of a first dance. If the organizers drop a licensed
   track at storage/music/first-dance.mp3 it plays instead, still behind
   the same toggle. Volumes stay low; the choice is remembered. */
const WedSound = (function () {
    let ctx = null, master = null, musicBus = null, verb = null, verbSend = null;
    let muted = true, musicOn = false, musicTimer = 0, nextNoteAt = 0; /* audio off by request */
    let chord = 0, step = 0, track = null, trackTried = false;
    const TRACK_URL = '{{ asset('storage/music/first-dance.mp3') }}';

    function ensure() {
        if (ctx) { if (ctx.state === 'suspended') ctx.resume().catch(function () {}); return ctx; }
        const AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return null;
        try { ctx = new AC(); } catch (e) { return null; }
        master = ctx.createGain();
        master.gain.value = muted ? 0 : 1;
        master.connect(ctx.destination);
        musicBus = ctx.createGain();
        musicBus.gain.value = 0.0001;
        musicBus.connect(master);
        /* a small warm room — generated impulse through a convolver */
        try {
            const irLen = Math.floor(ctx.sampleRate * 1.7);
            const ir = ctx.createBuffer(2, irLen, ctx.sampleRate);
            for (let ch = 0; ch < 2; ch++) {
                const d = ir.getChannelData(ch);
                for (let i = 0; i < irLen; i++) d[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / irLen, 2.6);
            }
            verb = ctx.createConvolver(); verb.buffer = ir;
            verbSend = ctx.createGain(); verbSend.gain.value = 0.28;
            verbSend.connect(verb); verb.connect(master);
        } catch (e) { verb = null; verbSend = null; }
        return ctx;
    }

    /* one soft bell tone (fundamental + a whisper of octave shimmer) */
    function tone(freq, when, dur, vol, type, bus) {
        if (!ctx) return;
        const t = Math.max(when, ctx.currentTime);
        bus = bus || master;
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.type = type || 'sine';
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.0001, t);
        g.gain.exponentialRampToValueAtTime(vol, t + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
        o.connect(g); g.connect(bus);
        o.start(t); o.stop(t + dur + 0.05);

        const o2 = ctx.createOscillator(), g2 = ctx.createGain();
        o2.type = 'sine';
        o2.frequency.setValueAtTime(freq * 2, t);
        g2.gain.setValueAtTime(0.0001, t);
        g2.gain.exponentialRampToValueAtTime(vol * 0.28, t + 0.02);
        g2.gain.exponentialRampToValueAtTime(0.0001, t + dur * 0.65);
        o2.connect(g2); g2.connect(bus);
        o2.start(t); o2.stop(t + dur * 0.65 + 0.05);
    }

    /* — Karplus–Strong nylon string: a real plucked-guitar timbre — */
    const pluckCache = {};
    function pluckBuffer(freq) {
        const key = Math.round(freq);
        if (pluckCache[key]) return pluckCache[key];
        const sr = ctx.sampleRate;
        const N = Math.max(2, Math.round(sr / freq));
        const dur = 2.6, len = Math.floor(sr * dur);
        const buf = ctx.createBuffer(1, len, sr);
        const out = buf.getChannelData(0);
        const ring = new Float32Array(N);
        let lp = 0;
        /* softened noise excitation = warm fingertip, not a plectrum */
        for (let i = 0; i < N; i++) { lp = 0.55 * lp + 0.45 * (Math.random() * 2 - 1); ring[i] = lp; }
        let idx = 0, prev = 0;
        for (let s = 0; s < len; s++) {
            const cur = ring[idx];
            out[s] = cur;
            ring[idx] = (cur + prev) * 0.5 * 0.996;
            prev = cur;
            idx = idx + 1 === N ? 0 : idx + 1;
        }
        const fade = Math.floor(sr * 0.3);
        for (let s = 0; s < fade; s++) out[len - fade + s] *= 1 - s / fade;
        pluckCache[key] = buf;
        return buf;
    }

    function pick(freq, when, vol, bus) {
        if (!ensure()) return;
        const t = Math.max(when || 0, ctx.currentTime) + 0.01;
        const src = ctx.createBufferSource();
        src.buffer = pluckBuffer(freq);
        const g = ctx.createGain();
        g.gain.setValueAtTime(vol, t);
        src.connect(g); g.connect(bus || master);
        if (verbSend) g.connect(verbSend);
        src.start(t);
    }

    function strum(freqs, vol, gap) {
        if (!ensure()) return;
        const t0 = ctx.currentTime + 0.02;
        freqs.forEach(function (f, i) { pick(f, t0 + i * (gap || 0.045), vol * (1 - i * 0.06)); });
    }

    /* slow first-dance picking: G – D/F# – Em7 – Cadd9 (bass + inner strings) */
    const CHORDS = [
        [98.00, 246.94, 293.66, 392.00],   // G
        [92.50, 220.00, 293.66, 369.99],   // D/F#
        [82.41, 246.94, 293.66, 392.00],   // Em7
        [130.81, 196.00, 293.66, 329.63],  // Cadd9
    ];
    const STEP = 0.46; /* unhurried eighth-note sway */

    function musicTick() {
        if (!musicOn || !ctx || track) return; /* real track takes over when present */
        while (nextNoteAt < ctx.currentTime + 1.2) {
            const c = CHORDS[chord];
            const pattern = [0, 2, 3, 1, 2, 3, 1, 2]; /* bass–mid–high fingerpicking */
            const i = step % pattern.length;
            const jitter = (Math.random() - 0.5) * 0.012; /* human feel */
            pick(c[pattern[i]], nextNoteAt + jitter, i === 0 ? 0.085 : 0.062, musicBus);
            /* sparse melody sparkle every other bar */
            if (i === 3 && (step / pattern.length) % 2 === 1) {
                pick(c[3] * 1.5, nextNoteAt + 0.2, 0.04, musicBus);
            }
            nextNoteAt += STEP;
            step++;
            if (i === pattern.length - 1) chord = (chord + 1) % CHORDS.length;
        }
    }

    function renderToggle() {
        const btn = document.getElementById('sound-toggle');
        if (!btn) return;
        btn.classList.toggle('is-off', muted);
        btn.setAttribute('aria-pressed', muted ? 'false' : 'true');
        const i = btn.querySelector('i');
        if (i) i.className = muted ? 'bi bi-mute-fill' : 'bi bi-volume-up-fill';
    }

    function bindToggle() {
        const btn = document.getElementById('sound-toggle');
        if (!btn || btn.dataset.bound) return;
        btn.dataset.bound = '1';
        renderToggle();
        btn.addEventListener('click', function () {
            muted = !muted;
            try { localStorage.setItem('wedSoundMuted', muted ? '1' : '0'); } catch (e) {}
            ensure();
            if (ctx) master.gain.setTargetAtTime(muted ? 0 : 1, ctx.currentTime, 0.04);
            renderToggle();
            if (!muted) { /* give the toggle an audible response */
                tone(880, 0, 0.5, 0.05);
                if (!musicOn) api.startMusic();
            } else if (musicOn) { api.stopMusic(); }
        });
    }

    /* unlock on the first real gesture (autoplay policy) */
    document.addEventListener('pointerdown', function once() {
        document.removeEventListener('pointerdown', once);
        ensure();
        let seen = false;
        try { seen = sessionStorage.getItem('weddingIntroSeen') === '1'; } catch (e) {}
        if (document.querySelector('[data-autoshow]') && !muted) api.success(); /* returned from RSVP/wish */
        if (seen && !muted) api.startMusic(); /* intro already watched — get straight to it */
    }, { once: true });

    document.addEventListener('visibilitychange', function () {
        if (!ctx) return;
        if (document.hidden) { ctx.suspend().catch(function () {}); }
        else if (!muted) { ctx.resume().catch(function () {}); }
    });

    /* a licensed first-dance track wins over the synth, when present */
    let trackEl = null;
    function tryTrack() {
        if (trackEl || trackTried) return !!trackEl;
        trackTried = true;
        try {
            const a = new Audio();
            a.preload = 'metadata';
            a.loop = true;
            a.volume = 0;
            a.addEventListener('canplay', function () { trackEl = a; }, { once: true });
            a.addEventListener('error', function () { trackEl = null; });
            a.src = TRACK_URL;
        } catch (e) { trackEl = null; }
        return !!trackEl;
    }

    const api = {
        bindToggle: bindToggle,
        envelope: function () { if (!muted) { strum([196.00, 246.94, 293.66, 392.00], 0.09, 0.05); } },   /* soft G up-stroke */
        reveal:   function () { if (!muted) { strum([196.00, 293.66, 392.00, 493.88], 0.085, 0.06); } }, /* Gsus-ish lift */
        pop:      function (i) { if (!muted) pick([329.63, 392.00, 440.00, 493.88, 587.33][i % 5], 0, 0.05); },
        inviteOpen: function () { if (!muted) { strum([164.81, 246.94, 329.63], 0.08, 0.07); } },        /* Em7 shimmer */
        success:  function () { if (!muted) { strum([130.81, 196.00, 261.63, 329.63], 0.085, 0.06); pick(523.25, 0.5, 0.05); } }, /* Cadd9 lift */
        siteOpen: function () {
            if (muted) return;
            strum([98.00, 246.94, 293.66, 392.00, 493.88], 0.075, 0.05); /* full G, then sway in */
            api.startMusic();
        },
        startMusic: function () {
            if (muted || musicOn || !ensure()) return;
            musicOn = true;
            if (tryTrack()) {
                trackEl.play().catch(function () {});
                /* fade the track in so it never bursts in */
                trackEl.volume = 0;
                const fadeIn = setInterval(function () {
                    if (!musicOn || !trackEl) { clearInterval(fadeIn); return; }
                    trackEl.volume = Math.min(0.55, trackEl.volume + 0.02);
                    if (trackEl.volume >= 0.55) clearInterval(fadeIn);
                }, 120);
                return;
            }
            nextNoteAt = ctx.currentTime + 0.15;
            musicBus.gain.setTargetAtTime(0.9, ctx.currentTime, 1.6);
            musicTick();
            musicTimer = setInterval(musicTick, 400);
        },
        stopMusic: function () {
            musicOn = false;
            clearInterval(musicTimer);
            if (trackEl) trackEl.pause();
            if (ctx) musicBus.gain.setTargetAtTime(0.0001, ctx.currentTime, 0.25);
        },
    };
    return api;
})();
WedSound.bindToggle();

/* ═══ Stars ═══ */
function createStars() {
    const container = document.getElementById('starsContainer');
    if (!container) return;
    for (let i = 0; i < 150; i++) {
        const star = document.createElement('div');
        star.className = Math.random() > 0.85 ? 'star star-gold' : 'star';
        const size = Math.random() * 2.5 + 0.5;
        star.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--duration:${1+Math.random()*3}s;--delay:${Math.random()*3}s;--min-opacity:${0.2+Math.random()*0.3};--max-opacity:${0.7+Math.random()*0.3};`;
        container.appendChild(star);
    }
    for (let i = 0; i < 3; i++) {
        const shoot = document.createElement('div');
        shoot.className = 'shooting-star';
        shoot.style.cssText = `left:${30+Math.random()*40}%;top:${10+Math.random()*30}%;animation-delay:${i*4+Math.random()*2}s;animation-duration:${2+Math.random()*2}s;`;
        container.appendChild(shoot);
    }
}
createStars();

function countdown(weddingDate) {
    return {
        days:'00',hours:'00',minutes:'00',seconds:'00',
        init(){this.update();setInterval(()=>this.update(),1000);},
        update(){const d=new Date(weddingDate)-new Date();if(d<=0)return;this.days=String(Math.floor(d/864e5)).padStart(2,'0');this.hours=String(Math.floor(d%864e5/36e5)).padStart(2,'0');this.minutes=String(Math.floor(d%36e5/6e4)).padStart(2,'0');this.seconds=String(Math.floor(d%6e4/1e3)).padStart(2,'0');}
    };
}

/* ═══ RSVP wizard (embedded in the RSVP section) ═══ */
function rsvpWizard() {
    return {
        step: @js($resumeStep ?? 1),
        totalSteps: 5,
        stepLabels: ["Guest details", "How you know us", "Who you're here for", "Will you join?", "Your message"],
        /* values the guest already typed before a validation bounce */
        name: @js(old('name', '')),
        email: @js(old('email', '')),
        phone: @js(old('phone', '')),
        relationship: @js(old('relationship', '')),
        relationshipOther: @js(old('relationship_other', '')),
        supporting: @js(old('supporting', '')),
        attendance: @js(old('attendance', '')),
        guestCount: @js((int) old('guest_count', 1)),
        message: @js(old('message', '')),
        submitForm() { this.$refs.rsvpForm.submit(); },
        confettiBurst() {
            const colors = ['#c9a84c', '#1a3c2a', '#f0e0d0', '#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6'];
            for (let i = 0; i < 30; i++) {
                const p = document.createElement('div');
                p.className = 'confetti-particle';
                p.style.left = (50 + (Math.random() - 0.5) * 60) + '%';
                p.style.top = '40%';
                p.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                p.style.animationDuration = (0.5 + Math.random() * 0.8) + 's';
                p.style.transform = `translateX(${(Math.random() - 0.5) * 200}px)`;
                document.body.appendChild(p);
                setTimeout(() => p.remove(), 1500);
            }
        }
    };
}

/* ═══ Album strip: continuous film drift — the hero photos never stop moving ═══ */
(function () {
    const track = document.getElementById('album-track');
    if (!track) return;
    const originals = Array.from(track.children);
    const n = originals.length;
    if (n < 1) return;
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* duplicate the set so the drift can loop seamlessly */
    if (n > 1) originals.forEach(s => track.appendChild(s.cloneNode(true)));

    const dotsBox = document.getElementById('album-dots');
    const dots = [];
    if (dotsBox && n > 1) {
        originals.forEach((_, i) => {
            const d = document.createElement('span');
            d.className = 'album-dot' + (i === 0 ? ' on' : '');
            d.addEventListener('click', () => {
                /* glide to that slide in the first copy of the set */
                track.scrollTo({ left: originals[i].offsetLeft - (track.clientWidth - originals[i].offsetWidth) / 2, behavior: 'smooth' });
            });
            dotsBox.appendChild(d);
            dots.push(d);
        });
    }

    const speed = 0.55; /* px per frame (~33px/s at 60fps) */
    let raf = 0, lastTs = 0, userScrolling = false, userTimer = 0;

    /* Drift only while the hero is actually on screen and the tab is visible —
       no silent work burning battery when the guest is further down the page. */
    let heroVisible = true, tabVisible = !document.hidden;
    const heroSection = document.getElementById('hero');
    if (heroSection && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (es) { heroVisible = es[0].isIntersecting; }, { threshold: 0.05 }).observe(heroSection);
    }
    document.addEventListener('visibilitychange', function () { tabVisible = !document.hidden; });

    /* wrap point = where the cloned set begins (width of one full set incl. gaps) */
    const firstClone = n > 1 ? track.children[n] : null;
    const wrapAt = () => (firstClone ? firstClone.offsetLeft : Infinity);

    function step(ts) {
        if (heroVisible && tabVisible && !userScrolling) {
            const dt = lastTs ? Math.min(64, ts - lastTs) : 16;
            lastTs = ts;
            track.scrollLeft += speed * (dt / 16.7);
            /* past the first copy of the set: snap back invisibly to the identical spot */
            const w = wrapAt();
            if (track.scrollLeft >= w - 1) track.scrollLeft -= w;
            syncDots();
        } else {
            lastTs = ts;
        }
        raf = requestAnimationFrame(step);
    }

    function syncDots() {
        if (!dots.length) return;
        const mid = track.scrollLeft + track.clientWidth / 2;
        let best = 0, bd = Infinity;
        originals.forEach((s, i) => {
            const d = Math.abs(s.offsetLeft + s.offsetWidth / 2 - mid);
            if (d < bd) { bd = d; best = i; }
        });
        dots.forEach((d, k) => d.classList.toggle('on', k === best));
    }

    /* touching the strip hands control to the guest; drift resumes after a pause */
    ['pointerdown', 'touchstart', 'wheel'].forEach(ev =>
        track.addEventListener(ev, () => {
            userScrolling = true;
            clearTimeout(userTimer);
            userTimer = setTimeout(() => { userScrolling = false; }, 2600);
        }, { passive: true })
    );

    if (!reduce && n > 1) raf = requestAnimationFrame(step);
})();

/* ═══ Reveal-on-scroll ═══ */
(function () {
    const els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in-view')); return; }
    const io = new IntersectionObserver(entries => {
        entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in-view'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    els.forEach(e => io.observe(e));
})();

/* ═══ Milestone photo rotators: cycle through every uploaded photo ═══ */
(function () {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('[data-rotator]').forEach(function (box) {
        const imgs = Array.from(box.querySelectorAll('img'));
        const dots = Array.from(box.querySelectorAll('.sdot'));
        if (imgs.length < 2) return;
        let i = 0, timer = 0;

        function show(n) {
            imgs[i].classList.remove('is-active');
            if (dots[i]) dots[i].classList.remove('on');
            i = ((n % imgs.length) + imgs.length) % imgs.length;
            imgs[i].classList.add('is-active');
            if (dots[i]) dots[i].classList.add('on');
        }
        function play() {
            clearInterval(timer);
            if (reduce || !inView) return;
            timer = setInterval(function () { show(i + 1); }, 3200);
        }

        /* tap to advance, hover to pause */
        box.addEventListener('click', function () { show(i + 1); play(); });
        box.addEventListener('mouseenter', function () { clearInterval(timer); });
        box.addEventListener('mouseleave', play);

        /* only rotate while this rotator is on screen and the tab is visible */
        let inView = false;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (es) {
                inView = es[0].isIntersecting;
                if (inView) { play(); } else { clearInterval(timer); }
            }, { threshold: 0.15 }).observe(box);
        } else {
            inView = true;
            play();
        }
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) { clearInterval(timer); } else { play(); }
        });
    });
})();

/* ═══ Dot-nav sync (desktop) ═══ */
(function () {
    const dots = document.querySelectorAll('#dot-nav .dot');
    if (!dots.length || !('IntersectionObserver' in window)) return;
    const current = { id: '' };
    const io = new IntersectionObserver(entries => {
        entries.forEach(en => { if (en.isIntersecting) current.id = en.target.id; });
        dots.forEach(d => d.classList.toggle('on', d.getAttribute('data-navlink') === current.id));
    }, { rootMargin: '-35% 0px -55% 0px' });
    document.querySelectorAll('section[id]').forEach(s => io.observe(s));
})();

/* ═══ Opening intro ═══ */
(function () {
    const intro = document.getElementById('wedding-intro');
    if (!intro) return;
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let finished = false;
    let runId = 0; /* bumped on every (re)play — stale timers from an old run become no-ops */
    const replayPill = document.getElementById('intro-replay');

    function markSeen() { try { sessionStorage.setItem('weddingIntroSeen', '1'); } catch (e) {} }
    function clearSeen() { try { sessionStorage.removeItem('weddingIntroSeen'); } catch (e) {} }
    function armReplayPill() {
        if (!replayPill) return;
        replayPill.hidden = false;
        requestAnimationFrame(() => replayPill.classList.add('show'));
    }

    if (replayPill) {
        replayPill.addEventListener('click', function () { startIntro(true); });
    }

    function resetIntro() {
        /* restore the envelope stage to its pristine pre-open state */
        finished = false;
        intro.classList.remove('photo-stage-on');
        intro.classList.remove('is-done');
        const env = document.getElementById('intro-envelope');
        if (env) {
            env.classList.remove('opening');
            delete env.dataset.opened;
        }
        const cover = document.getElementById('reveal-cover');
        if (cover) {
            cover.classList.remove('open');
            /* re-arm the tap listeners that were consumed with { once: true }:
               swap in a clean clone (cloneNode copies attributes, so clear the
               bound flag or initReveal would skip rebinding) */
            const fresh = cover.cloneNode(true);
            delete fresh.dataset.bound;
            cover.parentNode.replaceChild(fresh, cover);
        }
        document.querySelectorAll('.intro-popup').forEach(el => el.remove());
        const img = document.querySelector('.scratch-img');
        if (img) img.classList.remove('pop');
        const flash = document.querySelector('.reveal-flash');
        if (flash) flash.classList.remove('go');
    }

    function startIntro(fresh) {
        runId++;
        if (replayPill) replayPill.classList.remove('show');
        if (fresh) resetIntro();
        clearSeen();
        finished = false;
        lockScroll();
        seedFloaties();
        preloadIntroImages();
        intro.classList.add('is-on');
        setTimeout(() => intro.classList.add('env-stage-on'), 350);
        initReveal();
        initEnvelope();
    }

    function finish() {
        if (finished) return;
        finished = true;
        markSeen();
        WedSound.siteOpen();
        document.body.style.overflow = '';
        intro.classList.remove('env-stage-on', 'photo-stage-on');
        intro.classList.add('is-done');
        setTimeout(() => {
            /* a replay may have restarted the intro inside the 850ms fade — don't kill it */
            if (finished && intro.classList.contains('is-done')) {
                intro.classList.remove('is-on');
                armReplayPill();
            }
        }, 850);
    }

    function lockScroll() { document.body.style.overflow = 'hidden'; }

    function burst() {
        const spark = document.getElementById('intro-spark');
        if (!spark || reduce) return;
        const glyphs = ['✦', '♥', '❀', '✿', '✧'];
        for (let i = 0; i < 34; i++) {
            const b = document.createElement('i');
            const ang = Math.random() * Math.PI * 2;
            const dist = 80 + Math.random() * 180;
            const g = glyphs[i % glyphs.length];
            b.className = 'spark-bit' + (g === '♥' ? ' spark-heart' : (g === '❀' || g === '✿' ? ' spark-petal' : ''));
            b.textContent = g;
            b.style.setProperty('--dx', Math.cos(ang) * dist + 'px');
            b.style.setProperty('--dy', (Math.sin(ang) * dist - 30) + 'px');
            b.style.setProperty('--sc', (0.6 + Math.random() * 0.9).toFixed(2));
            b.style.setProperty('--spin', ((Math.random() * 300 - 150) | 0) + 'deg');
            b.style.fontSize = (10 + Math.random() * 10) + 'px';
            spark.appendChild(b);
            setTimeout(() => { if (b.parentNode) b.parentNode.removeChild(b); }, 1400);
        }
    }

    function openEnvelope() {
        const env = document.getElementById('intro-envelope');
        if (!env || env.dataset.opened) return;
        env.dataset.opened = '1';
        WedSound.envelope();
        burst();
        env.classList.add('opening');
        const myRun = runId;
        setTimeout(() => { if (myRun === runId) showPhotoStage(); }, 700);
    }

    function showPhotoStage() {
        if (finished) return;
        intro.classList.remove('env-stage-on');
        intro.classList.add('photo-stage-on');
        scheduleAutoReveal();
    }

    /* gentle auto-continue if the reveal photo is never tapped */
    function scheduleAutoReveal() {
        const cover = document.getElementById('reveal-cover');
        if (!cover) return;
        const myRun = runId;
        setTimeout(function () {
            if (myRun === runId && !finished && !cover.classList.contains('open')) cover.click();
        }, 4500);
    }

    /* Popup memory flash — positions are clamped so photos never leave the screen */
    @php
        $popupList = collect();
        foreach ($wedding->introImages as $im) { $popupList->push($im->image_path); }
        foreach (\App\Models\Photo::approved()->latest()->take(6)->pluck('file_path') as $p) { $popupList->push($p); }
        foreach ($wedding->heroImages->take(4)->pluck('image_path') as $p) { $popupList->push($p); }
        $popupList = $popupList->unique()->take(8)->values();
    @endphp
    const popupPhotos = @json($popupList->map(fn($p) => asset('storage/' . $p))->all());

    /* warm the image cache at intro start so the reveal photo and the popup
       memories are already decoded when their moment comes — nothing blank */
    function preloadIntroImages() {
        const scratch = document.querySelector('.scratch-img');
        if (scratch && scratch.src) { const warm = new Image(); warm.src = scratch.src; }
        popupPhotos.forEach(function (src) { const warm = new Image(); warm.src = src; });
    }

    function playPopups() {
        const box = document.getElementById('intro-popups');
        const myRun = runId;
        if (!box) { finish(); return; }
        if (!popupPhotos.length || reduce) { setTimeout(function () { if (myRun === runId) finish(); }, 600); return; }
        if (box.childElementCount) return; /* already mid-burst */

        /* safe anchors: 16–84% x, 18–82% y keeps a 26vw/150px card fully inside the frame */
        const anchors = [
            { x: 18, y: 20 }, { x: 82, y: 22 }, { x: 18, y: 76 }, { x: 82, y: 78 },
            { x: 50, y: 18 }, { x: 16, y: 48 }, { x: 84, y: 50 }, { x: 50, y: 82 }
        ];

        /* memory burst: every photo pops up as one collage (tiny cascade),
           holds together, then releases the guest straight into the site */
        popupPhotos.slice(0, 8).forEach(function (src, i) {
            setTimeout(function () {
                if (myRun !== runId) return;
                const a = anchors[i % anchors.length];
                const el = document.createElement('div');
                el.className = 'intro-popup';
                el.style.left = a.x + '%';
                el.style.top = a.y + '%';
                const im = document.createElement('img');
                im.onerror = function () { if (el.parentNode) el.parentNode.removeChild(el); };
                im.src = src;
                el.appendChild(im);
                box.appendChild(el);
                WedSound.pop(i);
            }, i * 90);
        });

        setTimeout(function () { if (myRun === runId) finish(); }, 90 * Math.min(popupPhotos.length, 8) + 2500);
    }

    function initReveal() {
        const cover = document.getElementById('reveal-cover');
        const img = document.querySelector('.scratch-img');
        const flash = document.querySelector('.reveal-flash');
        if (!cover) { playPopups(); return; }
        if (cover.dataset.bound) return; /* listeners already attached */
        cover.dataset.bound = '1';
        /* white petals raining down the gold cover — it feels like unwrapping a gift */
        if (!reduce && !cover.querySelector('.cover-petals')) {
            const rain = document.createElement('div');
            rain.className = 'cover-petals';
            rain.setAttribute('aria-hidden', 'true');
            for (let i = 0; i < 10; i++) {
                const s = document.createElement('span');
                s.textContent = i % 3 === 0 ? '❀' : '✿';
                s.style.left = (4 + Math.random() * 92) + '%';
                s.style.setProperty('--pd', (2.6 + Math.random() * 2.4).toFixed(1) + 's');
                s.style.setProperty('--pdl', (-Math.random() * 4).toFixed(1) + 's');
                rain.appendChild(s);
            }
            cover.insertBefore(rain, cover.firstChild);
        }
        const go = () => {
            cover.classList.add('open');
            if (flash) flash.classList.add('go');
            if (img) img.classList.add('pop');
            WedSound.reveal();
            burst(); /* petals + sparks as the photo appears */
            setTimeout(playPopups, 500);
        };
        cover.addEventListener('click', go, { once: true });
        cover.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') go(); }, { once: true });
    }

    function initEnvelope() {
        const envBtn = document.getElementById('intro-envelope');
        if (envBtn && !envBtn.dataset.envBound) {
            envBtn.dataset.envBound = '1';
            envBtn.addEventListener('click', openEnvelope);
            envBtn.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') openEnvelope(); });
        }
    }
    const skip = null; /* skip button removed — intro plays in full */

    /* ═══ Invitation card: save as a print-ready A4 PDF ═══ */
    function loadScriptOnce(src) {
        window.__wedScripts = window.__wedScripts || {};
        if (!window.__wedScripts[src]) {
            window.__wedScripts[src] = new Promise(function (res, rej) {
                const s = document.createElement('script');
                s.src = src;
                s.onload = res;
                s.onerror = function () { rej(new Error('could not load ' + src)); };
                document.head.appendChild(s);
            });
        }
        return window.__wedScripts[src];
    }
    function ensureHtml2Canvas() {
        return window.html2canvas ? Promise.resolve() : loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
    }
    function ensureJsPDF() {
        return (window.jspdf && window.jspdf.jsPDF) ? Promise.resolve() : loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
    }

    function downloadInvitation(btn) {
        const card = document.getElementById('invitation-card');
        if (!card || btn.dataset.busy) return;
        btn.dataset.busy = '1';
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Preparing…';
        const reset = function () { btn.dataset.busy = ''; btn.innerHTML = original; };

        const run = function () {
            /* keep the portrait shape in the captured image even if the text runs long */
            const prev = { ar: card.style.aspectRatio, mh: card.style.maxHeight };
            card.style.aspectRatio = 'auto';
            card.style.maxHeight = 'none';
            card.style.minHeight = Math.round(card.offsetWidth * 7 / 5) + 'px';
            html2canvas(card, { scale: 3, backgroundColor: '#fffdf6', useCORS: true, logging: false })
                .then(function (canvas) {
                    const names = (card.dataset.names || 'wedding-invitation')
                        .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true });

                    /* warm ivory page with a double gold frame */
                    pdf.setFillColor(253, 248, 240);
                    pdf.rect(0, 0, 210, 297, 'F');
                    pdf.setDrawColor(201, 168, 76);
                    pdf.setLineWidth(0.4);
                    pdf.rect(5, 5, 200, 287);
                    pdf.setLineWidth(1.1);
                    pdf.rect(6.6, 6.6, 196.8, 283.8);

                    /* fit the card inside the frame, keeping its aspect */
                    const maxW = 184, maxH = 268;
                    const ratio = canvas.width / canvas.height;
                    let w = maxW, h = w / ratio;
                    if (h > maxH) { h = maxH; w = h * ratio; }
                    pdf.addImage(canvas.toDataURL('image/jpeg', 0.94), 'JPEG',
                        (210 - w) / 2, (297 - h) / 2, w, h, undefined, 'FAST');

                    const date = (card.dataset.date || '').trim();
                    if (date) {
                        pdf.setFont('helvetica', 'normal');
                        pdf.setFontSize(9);
                        pdf.setTextColor(150, 140, 110);
                        pdf.text((card.dataset.names || '') + '  \u00b7  ' + date, 105, 288, { align: 'center' });
                    }
                    pdf.save(names + '-invitation.pdf');
                    reset();
                    burst();
                })
                .catch(reset)
                .finally(function () {
                    card.style.aspectRatio = prev.ar;
                    card.style.maxHeight = prev.mh;
                    card.style.minHeight = '';
                });
        };

        Promise.all([ensureHtml2Canvas(), ensureJsPDF()]).then(run).catch(reset);
    }

    /* ═══ Open the invitation anytime from the hero ═══ */
    const modal = document.getElementById('invite-modal');
    const openBtn = document.getElementById('open-invitation');
    if (modal && openBtn) {
        openBtn.addEventListener('click', function () {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            WedSound.inviteOpen();
        });

        function closeInviteModal() {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        document.getElementById('invite-modal-close').addEventListener('click', closeInviteModal);
        document.getElementById('invite-modal-download').addEventListener('click', function () {
            downloadInvitation(this);
        });
        /* the on-card RSVP pill: close the overlay, then let the anchor scroll to #rsvp */
        const icRsvp = document.getElementById('ic-rsvp');
        if (icRsvp) icRsvp.addEventListener('click', closeInviteModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) closeInviteModal(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display !== 'none') closeInviteModal();
        });
    }

    /* drifting glow-sparkles behind the envelope (pure CSS, cheap) */
    function seedFloaties() {
        const box = document.getElementById('intro-env-floaties');
        if (!box || reduce || box.childElementCount) return;
        const glyphs = ['✦', '♥', '✧', '❀'];
        for (let i = 0; i < 14; i++) {
            const s = document.createElement('i');
            s.className = 'env-floatie';
            s.textContent = glyphs[i % glyphs.length];
            s.style.setProperty('--x', (3 + Math.random() * 94).toFixed(1) + '%');
            s.style.setProperty('--fs', (8 + Math.random() * 9).toFixed(1) + 'px');
            s.style.setProperty('--dur', (7 + Math.random() * 9).toFixed(1) + 's');
            s.style.setProperty('--delay', (-Math.random() * 12).toFixed(1) + 's');
            s.style.setProperty('--op', (0.25 + Math.random() * 0.55).toFixed(2));
            s.style.setProperty('--rot', ((Math.random() * 220 - 110).toFixed(0)) + 'deg');
            box.appendChild(s);
        }
    }

    /* Every page load plays the intro in full — it's the invitation moment.
       Coming back with a section anchor (e.g. after submitting the RSVP) still
       lands straight on the page. The "Replay intro" pill remains for re-watching. */
    if (window.location.hash && window.location.hash !== '#top') {
        markSeen();
        armReplayPill();
    } else {
        startIntro(false);
    }
})();
</script>
@endpush
