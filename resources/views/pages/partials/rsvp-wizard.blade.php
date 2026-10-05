<div class="relative bg-white/85 backdrop-blur-xl rounded-[2rem] shadow-2xl shadow-primary/10 border border-secondary/15 p-5 sm:p-9 overflow-hidden" x-data="rsvpWizard()" x-cloak>

    {{-- ═══ SERVER-SIDE VALIDATION ERRORS ═══ --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 rounded-2xl px-4 py-3 text-sm mb-5">
            <p class="font-semibold mb-1"><i class="bi bi-exclamation-circle-fill mr-1"></i>Oops — please check:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $er)<li>{{ $er }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ═══ PROGRESS ═══ --}}
    <div x-show="step !== 'done'" x-cloak class="mb-6">
        <p class="text-center text-secondary/80 text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.35em] mb-4" x-text="stepLabels[step - 1]">Guest details</p>
        <div class="flex items-center justify-center">
            <template x-for="s in totalSteps" :key="s">
                <div class="flex items-center">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs font-bold ring-2 transition-all duration-500"
                         :class="step === s
                                        ? 'bg-secondary text-primary ring-secondary scale-110 shadow-lg shadow-secondary/40'
                                        : (step > s ? 'bg-secondary/15 text-secondary ring-secondary/40' : 'bg-white text-gray-400 ring-gray-200')"
                         x-text="s"></div>
                    <div x-show="s < totalSteps" class="w-5 sm:w-10 h-[3px] rounded-full mx-1 sm:mx-1.5 transition-colors duration-500"
                         :class="step > s ? 'bg-secondary/50' : 'bg-gray-200'"></div>
                </div>
            </template>
        </div>
    </div>

    <div class="relative">

        {{-- ═══ STEP 1: WHO ARE YOU? ═══ --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-10">
            <div class="text-center mb-7">
                <div class="w-16 h-16 mx-auto -rotate-6 rounded-2xl bg-gradient-to-br from-secondary to-secondary/80 text-primary flex items-center justify-center shadow-lg shadow-secondary/25 mb-5">
                    <i class="bi bi-person-heart text-3xl"></i>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">Hello there!</h3>
                <p class="text-gray-500 mt-2 text-sm">We can't wait to celebrate with you — tell us who you are.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Your Name</label>
                    <div class="relative">
                        <i class="bi bi-person text-gray-300 absolute left-4 top-1/2 -translate-y-1/2 text-lg"></i>
                        <input type="text" x-model="name" required
                               class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-2 border-gray-100 bg-ivory/50 focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10 outline-none transition text-lg font-medium text-primary placeholder:text-gray-300 placeholder:font-normal"
                               placeholder="What should we call you?">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Email <span class="normal-case text-gray-300 font-normal">(optional)</span></label>
                        <div class="relative">
                            <i class="bi bi-envelope text-gray-300 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            <input type="email" x-model="email"
                                   class="w-full pl-10 pr-3 py-3 rounded-2xl border-2 border-gray-100 bg-ivory/50 focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10 outline-none transition text-gray-700 placeholder:text-gray-300"
                                   placeholder="you@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Phone <span class="normal-case text-gray-300 font-normal">(optional)</span></label>
                        <div class="relative">
                            <i class="bi bi-telephone text-gray-300 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            <input type="text" x-model="phone"
                                   class="w-full pl-10 pr-3 py-3 rounded-2xl border-2 border-gray-100 bg-ivory/50 focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10 outline-none transition text-gray-700 placeholder:text-gray-300"
                                   placeholder="+254 7XX">
                        </div>
                    </div>
                </div>
            </div>

            <button @click="if(name) step = 2" :disabled="!name"
                    class="w-full mt-8 py-4 rounded-full font-bold text-sm uppercase tracking-[0.2em] transition-all duration-300 inline-flex items-center justify-center gap-2"
                    :class="name ? 'bg-gradient-to-r from-primary to-primary-light text-white shadow-xl shadow-primary/25 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                <span>Continue</span><i class="bi bi-arrow-right"></i>
            </button>
        </div>

        {{-- ═══ STEP 2: RELATIONSHIP ═══ --}}
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-10">
            <div class="text-center mb-7">
                <div class="w-16 h-16 mx-auto rotate-6 rounded-2xl bg-gradient-to-br from-primary to-primary-light text-white flex items-center justify-center shadow-lg shadow-primary/25 mb-5">
                    <i class="bi bi-people-fill text-3xl"></i>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">Lovely to meet you, <span class="text-secondary" x-text="name.split(' ')[0]"></span>!</h3>
                <p class="text-gray-500 mt-2 text-sm">How do you know the couple?</p>
            </div>

            <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                @foreach([
                    ['val' => 'friend', 'bi' => 'bi-people-fill', 'label' => 'Friend', 'color' => 'from-blue-400 to-indigo-500'],
                    ['val' => 'family', 'bi' => 'bi-house-heart-fill', 'label' => 'Family', 'color' => 'from-rose-400 to-pink-500'],
                    ['val' => 'colleague', 'bi' => 'bi-briefcase-fill', 'label' => 'Colleague', 'color' => 'from-violet-400 to-purple-500'],
                    ['val' => 'church', 'bi' => 'bi-bank2', 'label' => 'Church', 'color' => 'from-amber-400 to-orange-500'],
                    ['val' => 'neighbor', 'bi' => 'bi-house-door-fill', 'label' => 'Neighbor', 'color' => 'from-emerald-400 to-teal-500'],
                    ['val' => 'other', 'bi' => 'bi-stars', 'label' => 'Other', 'color' => 'from-cyan-400 to-blue-500'],
                ] as $opt)
                    <button type="button" @click="relationship = '{{ $opt['val'] }}'"
                            class="group relative flex flex-col items-center gap-2.5 py-4 sm:py-5 px-1 rounded-2xl border-2 bg-white transition-all duration-300"
                            :class="relationship === '{{ $opt['val'] }}' ? 'border-secondary shadow-lg shadow-secondary/10 scale-[1.03]' : 'border-gray-100 hover:border-secondary/30 hover:shadow-md'">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br {{ $opt['color'] }} text-white flex items-center justify-center shadow-md transition-transform duration-300"
                             :class="relationship === '{{ $opt['val'] }}' ? 'scale-110 -rotate-6' : 'group-hover:scale-105 group-hover:rotate-3'">
                            <i class="bi {{ $opt['bi'] }} text-xl sm:text-2xl"></i>
                        </div>
                        <span class="text-[11px] sm:text-sm font-semibold transition-colors" :class="relationship === '{{ $opt['val'] }}' ? 'text-primary' : 'text-gray-500'">{{ $opt['label'] }}</span>
                        <div x-show="relationship === '{{ $opt['val'] }}'" x-transition
                             class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-secondary rounded-full flex items-center justify-center text-primary text-xs shadow"><i class="bi bi-check-lg"></i></div>
                    </button>
                @endforeach
            </div>

            <div x-show="relationship === 'other'" x-transition x-cloak class="mt-4">
                <div class="relative">
                    <i class="bi bi-pencil text-gray-300 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="relationshipOther" placeholder="Tell us how you know them..."
                           class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-gray-100 bg-ivory/50 focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10 outline-none transition text-gray-700">
                </div>
            </div>

            <div class="flex gap-3 mt-7">
                <button @click="step = 1" class="px-6 py-4 rounded-full border-2 border-gray-100 text-gray-400 hover:text-primary hover:border-secondary/30 transition-all inline-flex items-center gap-2 text-sm font-semibold"><i class="bi bi-arrow-left"></i> Back</button>
                <button @click="if(relationship) step = 3" :disabled="!relationship"
                        class="flex-1 py-4 rounded-full font-bold text-sm uppercase tracking-[0.2em] transition-all duration-300 inline-flex items-center justify-center gap-2"
                        :class="relationship ? 'bg-gradient-to-r from-primary to-primary-light text-white shadow-xl shadow-primary/25 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <span>Continue</span><i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ═══ STEP 3: SUPPORTING ═══ --}}
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-10">
            <div class="text-center mb-7">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-secondary/25 to-secondary/5 text-secondary flex items-center justify-center shadow-inner mb-5">
                    <i class="bi bi-heart-fill text-2xl"></i>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">Who are you here for?</h3>
                <p class="text-gray-500 mt-2 text-sm">Pick who you're cheering on — or both!</p>
            </div>

            <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                <button type="button" @click="supporting = 'bride'"
                        class="group relative rounded-2xl border-2 p-4 sm:p-5 bg-white transition-all duration-300 flex flex-col items-center gap-3"
                        :class="supporting === 'bride' ? 'border-secondary shadow-xl shadow-secondary/10 scale-[1.04]' : 'border-gray-100 hover:border-secondary/30 hover:shadow-md'">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-rose-100 to-rose-200 text-rose-500 flex items-center justify-center shadow-sm transition-transform duration-300"
                         :class="supporting === 'bride' ? 'scale-110 -rotate-6 ring-4 ring-rose-200' : 'group-hover:scale-105'">
                        <i class="bi bi-person-standing-dress text-2xl sm:text-3xl"></i>
                    </div>
                    <span class="font-bold text-xs sm:text-sm" :class="supporting === 'bride' ? 'text-primary' : 'text-gray-500'">The Bride</span>
                    <div x-show="supporting === 'bride'" x-transition
                         class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-secondary rounded-full flex items-center justify-center text-primary text-xs shadow"><i class="bi bi-check-lg"></i></div>
                </button>

                <button type="button" @click="supporting = 'both'"
                        class="group relative rounded-2xl border-2 p-4 sm:p-5 bg-white transition-all duration-300 flex flex-col items-center gap-3"
                        :class="supporting === 'both' ? 'border-secondary shadow-xl shadow-secondary/10 scale-[1.04]' : 'border-gray-100 hover:border-secondary/30 hover:shadow-md'">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-secondary/15 text-secondary flex items-center justify-center shadow-sm transition-transform duration-300"
                         :class="supporting === 'both' ? 'scale-110 ring-4 ring-secondary/30' : 'group-hover:scale-105'">
                        <i class="bi bi-hearts text-2xl sm:text-3xl"></i>
                    </div>
                    <span class="font-bold text-xs sm:text-sm" :class="supporting === 'both' ? 'text-primary' : 'text-gray-500'">Both</span>
                    <div x-show="supporting === 'both'" x-transition
                         class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-secondary rounded-full flex items-center justify-center text-primary text-xs shadow"><i class="bi bi-check-lg"></i></div>
                </button>

                <button type="button" @click="supporting = 'groom'"
                        class="group relative rounded-2xl border-2 p-4 sm:p-5 bg-white transition-all duration-300 flex flex-col items-center gap-3"
                        :class="supporting === 'groom' ? 'border-secondary shadow-xl shadow-secondary/10 scale-[1.04]' : 'border-gray-100 hover:border-secondary/30 hover:shadow-md'">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-700 flex items-center justify-center shadow-sm transition-transform duration-300"
                         :class="supporting === 'groom' ? 'scale-110 rotate-6 ring-4 ring-emerald-200' : 'group-hover:scale-105'">
                        <i class="bi bi-person-standing text-2xl sm:text-3xl"></i>
                    </div>
                    <span class="font-bold text-xs sm:text-sm" :class="supporting === 'groom' ? 'text-primary' : 'text-gray-500'">The Groom</span>
                    <div x-show="supporting === 'groom'" x-transition
                         class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-secondary rounded-full flex items-center justify-center text-primary text-xs shadow"><i class="bi bi-check-lg"></i></div>
                </button>
            </div>

            <div class="flex gap-3 mt-7">
                <button @click="step = 2" class="px-6 py-4 rounded-full border-2 border-gray-100 text-gray-400 hover:text-primary hover:border-secondary/30 transition-all inline-flex items-center gap-2 text-sm font-semibold"><i class="bi bi-arrow-left"></i> Back</button>
                <button @click="if(supporting) step = 4" :disabled="!supporting"
                        class="flex-1 py-4 rounded-full font-bold text-sm uppercase tracking-[0.2em] transition-all duration-300 inline-flex items-center justify-center gap-2"
                        :class="supporting ? 'bg-gradient-to-r from-primary to-primary-light text-white shadow-xl shadow-primary/25 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <span>Continue</span><i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ═══ STEP 4: ATTENDING? ═══ --}}
        <div x-show="step === 4" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-10">
            <div class="text-center mb-7">
                <div class="w-16 h-16 mx-auto -rotate-3 rounded-full bg-gradient-to-br from-secondary to-secondary/80 text-primary flex items-center justify-center shadow-lg shadow-secondary/25 mb-5">
                    <i class="bi bi-balloon-heart text-3xl"></i>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">Will you be there?</h3>
                <p class="text-gray-500 mt-2 text-sm">The party wouldn't be the same without you — we really hope you can make it!</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
                <button type="button" @click="attendance = 'attending'; confettiBurst()"
                        class="relative rounded-2xl border-2 p-6 text-center transition-all duration-500 flex flex-col items-center gap-2"
                        :class="attendance === 'attending' ? 'border-green-400 bg-gradient-to-b from-green-50 to-white shadow-xl shadow-green-500/10 scale-[1.03]' : 'border-gray-100 bg-white hover:border-green-300 hover:shadow-md'">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center transition-all duration-500"
                         :class="attendance === 'attending' ? 'bg-green-100 scale-110' : 'bg-gray-100'">
                        <i class="bi bi-balloon-heart-fill text-4xl" :class="attendance === 'attending' ? 'text-green-500' : 'text-gray-300'"></i>
                    </div>
                    <span class="font-bold text-base" :class="attendance === 'attending' ? 'text-green-700' : 'text-gray-500'">I'll be there!</span>
                    <span class="text-xs" :class="attendance === 'attending' ? 'text-green-600' : 'text-gray-400'">Wouldn't miss it</span>
                    <div x-show="attendance === 'attending'" x-transition
                         class="absolute -top-2 -right-2 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center text-white text-sm shadow-lg"><i class="bi bi-check-lg"></i></div>
                </button>

                <button type="button" @click="attendance = 'not_attending'"
                        class="relative rounded-2xl border-2 p-6 text-center transition-all duration-500 flex flex-col items-center gap-2"
                        :class="attendance === 'not_attending' ? 'border-orange-300 bg-gradient-to-b from-orange-50 to-white shadow-xl shadow-orange-500/10 scale-[1.03]' : 'border-gray-100 bg-white hover:border-orange-200 hover:shadow-md'">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center transition-all duration-500"
                         :class="attendance === 'not_attending' ? 'bg-orange-100 scale-110' : 'bg-gray-100'">
                        <i class="bi bi-emoji-frown-fill text-4xl" :class="attendance === 'not_attending' ? 'text-orange-400' : 'text-gray-300'"></i>
                    </div>
                    <span class="font-bold text-base" :class="attendance === 'not_attending' ? 'text-orange-700' : 'text-gray-500'">Sadly, no</span>
                    <span class="text-xs" :class="attendance === 'not_attending' ? 'text-orange-600' : 'text-gray-400'">We'll miss you dearly</span>
                    <div x-show="attendance === 'not_attending'" x-transition
                         class="absolute -top-2 -right-2 w-7 h-7 bg-orange-400 rounded-full flex items-center justify-center text-white text-sm shadow-lg"><i class="bi bi-check-lg"></i></div>
                </button>
            </div>

            {{-- Guest count --}}
            <div x-show="attendance === 'attending'" x-transition x-cloak class="mt-6 p-5 rounded-2xl border-2 border-dashed border-green-200 bg-green-50/50">
                <p class="text-center text-gray-600 font-semibold mb-4 flex items-center justify-center gap-2"><i class="bi bi-people-fill text-green-500"></i> How many of you are coming?</p>
                <div class="flex items-center justify-center gap-4 sm:gap-6">
                    <button type="button" @click="guestCount = Math.max(1, guestCount - 1)"
                            class="w-12 h-12 rounded-full bg-white text-gray-500 hover:text-primary hover:border-secondary/40 shadow-md transition-all active:scale-90 flex items-center justify-center">
                        <i class="bi bi-dash-lg text-xl"></i>
                    </button>
                    <div class="w-20 text-center">
                        <div class="text-5xl font-playfair font-bold text-primary transition-all duration-300" x-text="guestCount">1</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-widest mt-1" x-text="guestCount === 1 ? 'person' : 'people'">people</div>
                    </div>
                    <button type="button" @click="guestCount = Math.min(10, guestCount + 1)"
                            class="w-12 h-12 rounded-full bg-white text-gray-500 hover:text-primary hover:border-secondary/40 shadow-md transition-all active:scale-90 flex items-center justify-center">
                        <i class="bi bi-plus-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="flex gap-3 mt-7">
                <button @click="step = 3" class="px-6 py-4 rounded-full border-2 border-gray-100 text-gray-400 hover:text-primary hover:border-secondary/30 transition-all inline-flex items-center gap-2 text-sm font-semibold"><i class="bi bi-arrow-left"></i> Back</button>
                <button @click="if(attendance) step = 5" :disabled="!attendance"
                        class="flex-1 py-4 rounded-full font-bold text-sm uppercase tracking-[0.2em] transition-all duration-300 inline-flex items-center justify-center gap-2"
                        :class="attendance ? 'bg-gradient-to-r from-primary to-primary-light text-white shadow-xl shadow-primary/25 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <span>Continue</span><i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- ═══ STEP 5: WELL WISH + SUBMIT ═══ --}}
        <div x-show="step === 5" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-x-10">
            <div class="text-center mb-7">
                <div class="w-16 h-16 mx-auto rotate-6 rounded-2xl bg-gradient-to-br from-primary to-primary-light text-white flex items-center justify-center shadow-lg shadow-primary/25 mb-5">
                    <i class="bi bi-chat-heart-fill text-2xl"></i>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">One last thing...</h3>
                <p class="text-gray-500 mt-2 text-sm">Leave a little note for the couple — or just say hi <span class="text-gray-400">(optional)</span></p>
            </div>

            <textarea x-model="message" rows="4"
                      class="w-full px-5 py-4 rounded-2xl border-2 border-gray-100 bg-ivory/50 focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10 outline-none transition resize-none text-gray-700 placeholder:text-gray-300"
                      placeholder="Write something sweet — we'll read every word..."></textarea>

            {{-- Summary --}}
            <div class="mt-5 rounded-2xl bg-gradient-to-br from-primary/5 to-secondary/5 border border-secondary/10 p-5">
                <p class="text-xs font-bold text-secondary uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><i class="bi bi-clipboard2-check"></i> Your RSVP</p>
                <div class="space-y-2 text-sm">
                    <p class="flex items-center gap-2 text-gray-600">
                        <i class="bi bi-person text-gray-300"></i>
                        <span class="font-medium text-gray-800" x-text="name"></span>
                    </p>
                    <p class="flex items-center gap-2 text-gray-600 capitalize">
                        <i class="bi bi-heart text-gray-300"></i>
                        <span x-text="supporting"></span>
                    </p>
                    <p class="flex items-center gap-2 text-gray-600">
                        <i class="bi bi-calendar2-check text-gray-300"></i>
                        <span class="font-medium" :class="attendance === 'attending' ? 'text-green-600' : 'text-orange-500'" x-text="attendance === 'attending' ? 'Attending' : 'Not attending'"></span>
                        <span x-show="attendance === 'attending'" class="text-gray-400 text-xs" x-text="'· ' + guestCount + (guestCount === 1 ? ' guest' : ' guests')"></span>
                    </p>
                </div>
            </div>

            <button @click="submitForm()"
                    class="w-full mt-6 py-4 rounded-full font-bold text-sm uppercase tracking-[0.2em] bg-gradient-to-r from-secondary to-secondary/85 text-primary shadow-xl shadow-secondary/25 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99] transition-all duration-300 inline-flex items-center justify-center gap-2">
                <span>Send RSVP</span><i class="bi bi-heart-fill"></i>
            </button>
            <button @click="step = 4" class="mt-3 w-full py-3 rounded-full border-2 border-gray-100 text-gray-400 hover:text-primary hover:border-secondary/30 transition-all inline-flex items-center justify-center gap-2 text-sm font-semibold"><i class="bi bi-arrow-left"></i> Back</button>
        </div>

        {{-- ═══ DONE ═══ --}}
        <div x-show="step === 'done'" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <div class="text-center py-4">
                <div class="relative w-28 h-28 mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full bg-secondary/30 animate-ping"></div>
                    <div class="absolute inset-2 rounded-full bg-secondary/20"></div>
                    <div class="relative w-full h-full rounded-full bg-gradient-to-b from-secondary to-secondary/80 flex items-center justify-center text-primary shadow-xl shadow-secondary/30">
                        <i class="bi bi-balloon-heart-fill text-5xl"></i>
                    </div>
                </div>
                <h3 class="font-playfair text-2xl sm:text-3xl text-primary">Thank You, <span class="text-secondary" x-text="name.split(' ')[0]"></span>!</h3>
                <p class="text-gray-600 mt-3 text-base">Your RSVP has been received.</p>
                <p x-show="attendance === 'attending'" class="text-secondary font-medium mt-1">We can't wait to celebrate with you! <i class="bi bi-cup-straw"></i></p>
                <p x-show="attendance === 'not_attending'" class="text-gray-500 mt-1">You'll be in our hearts and prayers.</p>
            </div>
        </div>

    </div>

    {{-- Hidden form for actual submission --}}
    <form x-ref="rsvpForm" action="{{ route('rsvp.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="name" :value="name">
        <input type="hidden" name="email" :value="email">
        <input type="hidden" name="phone" :value="phone">
        <input type="hidden" name="relationship" :value="relationship">
        <input type="hidden" name="relationship_other" :value="relationshipOther">
        <input type="hidden" name="supporting" :value="supporting">
        <input type="hidden" name="attendance" :value="attendance">
        <input type="hidden" name="guest_count" :value="guestCount">
        <input type="hidden" name="message" :value="message">
    </form>
</div>
