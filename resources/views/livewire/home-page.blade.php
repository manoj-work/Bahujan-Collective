@php $distanceOptions = [10, 25, 50, 100]; @endphp

@php $groupedEvents = $groupedEvents ?? []; @endphp

@php
    $hasMore = count($filteredEvents ?? []) > count($visibleEvents ?? []);
    $componentId = $this->getId();
    $calendarMonth = $activeDate['month'] ?? now()->month;
    $calendarYear = $activeDate['year'] ?? now()->year;
    $activeDay = $activeDate['day'] ?? now()->day;
    $calendarTitle = \Carbon\Carbon::create($calendarYear, $calendarMonth)->format('F Y');
    $daysInMonth = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->daysInMonth;
    $highlightDays = collect($visibleEvents ?? [])
        ->filter(fn ($event) => ($event['calendar_month'] ?? null) === $calendarMonth && ($event['calendar_year'] ?? null) === $calendarYear)
        ->map(fn ($event) => $event['calendar_day'] ?? null)
        ->filter()
        ->unique()
        ->values()
        ->all();
@endphp

<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-[120px]"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-cyan-400/10 blur-[120px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">
        <header class="flex flex-col gap-4 rounded-2xl border border-slate-800/70 bg-slate-900/70 p-5 backdrop-blur lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-sky-500 to-cyan-400 text-lg font-semibold text-white shadow-lg shadow-indigo-500/30">
                    BC
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Bahujan Collective</p>
                    <p class="text-xl font-semibold text-white">Community Events</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-sm">
                <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70" href="#">Home</a>
                <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70" href="#">Events</a>
                <a class="rounded-full bg-amber-400/90 px-4 py-2 font-semibold text-amber-900 shadow-sm shadow-amber-400/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-400/30" href="#">Job Board</a>
                <a class="rounded-full bg-lime-400/90 px-4 py-2 font-semibold text-lime-900 shadow-sm shadow-lime-300/50 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/40" href="#">Curated Music</a>
                <a class="rounded-full bg-rose-400/90 px-4 py-2 font-semibold text-rose-950 shadow-sm shadow-rose-300/60 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-rose-300/40" href="#">Helping Hand</a>
                <div class="relative flex items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80">
                    <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 5.64 5.64a7.5 7.5 0 0 0 11.02 11.02Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        class="bg-transparent text-sm placeholder:text-slate-500 focus:outline-none"
                        placeholder="Search events by title or location"
                        type="search"
                        aria-label="Search events"
                    />
                </div>
                <div class="flex items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-700/70 text-sm font-semibold">+</span>
                    <span class="text-sm font-semibold">My Agenda</span>
                    <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-rose-500 text-xs font-bold text-white">5</span>
                </div>
            </div>
        </header>

        <main class="grid items-start gap-6 lg:grid-cols-3">
            <section class="space-y-4 lg:col-span-2">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 backdrop-blur shadow-xl shadow-slate-900/50">
                    <div class="flex flex-col gap-4 border-b border-slate-800/60 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-1">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Events</p>
                            <p class="text-xl font-semibold text-white">Find community gatherings near you</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-200 ring-1 ring-emerald-500/40">Hybrid</span>
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-200 ring-1 ring-slate-700/80">April 2024</span>
                        </div>
                    </div>

                    {{-- Hidden: date, location, and distance filters
                    <div class="grid gap-4 border-b border-slate-800/60 px-6 py-5 sm:grid-cols-2 xl:grid-cols-3">
                        <div class="flex items-center gap-3 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 10H3m18 0v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9m18 0V7a2 2 0 0 0-2-2h-1m-2.5 0h-5M7 5H6a2 2 0 0 0-2 2v3m14-5V3m0 2H7m0-2v2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Date</p>
                                <p class="text-sm font-semibold text-white">Sun, Apr 28</p>
                            </div>
                            <button class="rounded-lg bg-slate-800/80 px-3 py-2 text-xs font-semibold text-slate-200 ring-1 ring-slate-700/80 transition hover:bg-slate-700/80">Open</button>
                        </div>

                        <div class="flex items-center gap-3 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500/20 text-sky-300">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="m18 20-3-3m0 0a6 6 0 1 0-12 0 6 6 0 0 0 12 0ZM22 6v6m-3-3h6" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">City / State / Country</p>
                                <p class="text-sm font-semibold text-white">Bangalore, India</p>
                            </div>
                            <button class="rounded-lg bg-slate-800/80 px-3 py-2 text-xs font-semibold text-slate-200 ring-1 ring-slate-700/80 transition hover:bg-slate-700/80">Set</button>
                        </div>

                        <div class="flex items-center gap-3 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Z" />
                                    <path d="m12 9-3 3 3 3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Distance</p>
                                <p class="text-sm font-semibold text-white">{{ $filters['distance'] }} km radius</p>
                            </div>
                            <div class="flex w-full flex-wrap justify-end gap-2 sm:w-auto sm:justify-start">
                                @foreach ($distanceOptions as $distance)
                                    <button
                                        wire:click="setDistance({{ $distance }})"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold ring-1 transition {{ $filters['distance'] === $distance ? 'bg-emerald-500 text-emerald-950 ring-emerald-400' : 'bg-slate-800/80 text-slate-200 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                        {{ $distance }} km
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    --}}

                    <div class="flex flex-wrap items-center gap-3 border-b border-slate-800/60 px-6 py-4">
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200 ring-1 ring-indigo-400/40">Mode</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($modeOptions as $key => $label)
                                <button
                                    wire:click="setMode('{{ $key }}')"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['mode'] === $key ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 px-6 py-4">
                        <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Categories</span>
                        <div class="flex flex-wrap gap-2">
                            <button
                                wire:click="setCategory('all')"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['category'] === 'all' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                All
                            </button>
                            @foreach ($categories as $key => $label)
                                <button
                                    wire:click="setCategory('{{ $key }}')"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['category'] === $key ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-5 px-6 py-6">
                        @forelse ($groupedEvents as $categoryKey => $events)
                            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 shadow-inner shadow-slate-900/40">
                                <div class="flex items-center justify-between border-b border-slate-800/60 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $categories[$categoryKey] ?? 'Events' }}</p>
                                    <div class="flex items-center gap-2 text-xs text-slate-400">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                                        Updated
                                    </div>
                                </div>
                                <div class="divide-y divide-slate-800/60">
                                    @foreach ($events as $event)
                                        <div
                                            class="flex flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:gap-6 event-observer"
                                            data-event-id="{{ $event['id'] }}"
                                            data-event-year="{{ $event['calendar_year'] ?? '' }}"
                                            data-event-month="{{ $event['calendar_month'] ?? '' }}"
                                            data-event-day="{{ $event['calendar_day'] ?? '' }}"
                                        >
                                            <div class="flex-1 space-y-2">
                                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-300">
                                                    <span class="rounded-full bg-slate-800/80 px-3 py-1 text-slate-200 ring-1 ring-slate-700/80">{{ $categories[$event['category']] ?? 'Event' }}</span>
                                                    <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-indigo-100 ring-1 ring-indigo-400/30">{{ str_replace('_', ' ', ucfirst($event['mode'])) }}</span>
                                                    <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-emerald-100 ring-1 ring-emerald-500/40">{{ $event['distance'] > 0 ? $event['distance'] . ' km' : 'Online' }}</span>
                                                </div>
                                                <p class="text-lg font-semibold text-white sm:text-xl">{{ $event['title'] }}</p>
                                                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <path d="M21 10H3m18 0v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9m18 0V7a2 2 0 0 0-2-2h-1m-2.5 0h-5M7 5H6a2 2 0 0 0-2 2v3m14-5V3m0 2H7m0-2v2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <span>{{ $event['date'] }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z" stroke-linecap="round" stroke-linejoin="round" />
                                                            <circle cx="12" cy="10" r="2.5" />
                                                        </svg>
                                                        <span>{{ $event['location'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between gap-3 sm:w-60 sm:flex-col sm:items-end">
                                                <div class="rounded-full bg-slate-800/80 px-3 py-1 text-xs font-semibold text-slate-200 ring-1 ring-slate-700/80">
                                                    {{ $event['rsvps'] }} RSVPs
                                                </div>
                                                <div class="flex gap-2">
                                                    <button class="rounded-full bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-500/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/50">
                                                        Going
                                                    </button>
                                                    <button class="rounded-full bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/80">
                                                        Interested
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 px-6 py-10 text-center text-slate-300">
                                No events match the selected filters yet. Try another mode or category.
                            </div>
                        @endforelse

                        <div class="flex items-center justify-center gap-3 pt-2">
                            @if ($hasMore)
                                <button
                                    wire:click="loadMore"
                                    class="rounded-full bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/80">
                                    Load more events
                                </button>
                            @else
                                <span class="text-xs uppercase tracking-[0.2em] text-slate-500">You’ve reached the latest events</span>
                            @endif
                        </div>
                        <div id="events-sentinel" class="h-12"></div>
                    </div>
                </div>
            </section>

            <aside class="space-y-4 lg:col-span-1 lg:sticky lg:top-6 self-start">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 p-5 shadow-xl shadow-slate-900/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Calendar</p>
                            <p class="text-lg font-semibold text-white">{{ $calendarTitle }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-slate-300">
                            <button class="rounded-lg bg-slate-800/80 px-2 py-1 ring-1 ring-slate-700/80 hover:bg-slate-700/80">‹</button>
                            <button class="rounded-lg bg-slate-800/80 px-2 py-1 ring-1 ring-slate-700/80 hover:bg-slate-700/80">›</button>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-7 gap-2 text-center text-sm text-slate-400">
                        <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                    </div>
                    <div class="mt-2 grid grid-cols-7 gap-2 text-center text-sm">
                        @foreach (range(1, $daysInMonth) as $day)
                            @php
                                $isHighlight = in_array($day, $highlightDays ?? [], true);
                                $isActive = $activeDay === $day && $calendarMonth === ($activeDate['month'] ?? null) && $calendarYear === ($activeDate['year'] ?? null);
                            @endphp
                            <button
                                wire:click="setSelectedDate({{ $calendarYear }}, {{ $calendarMonth }}, {{ $day }})"
                                class="flex aspect-square items-center justify-center rounded-lg border border-transparent text-sm font-semibold transition {{ $isActive ? 'bg-cyan-500 text-slate-900 ring-2 ring-cyan-200/80 shadow-md shadow-cyan-500/40' : ($isHighlight ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/40 ring-2 ring-indigo-300/70' : 'bg-slate-800/70 text-slate-200 ring-1 ring-slate-800/80 hover:bg-slate-700/70') }}"
                            >
                                {{ $day }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800/70 bg-gradient-to-br from-slate-900/80 via-slate-900/60 to-slate-950 p-5 shadow-xl shadow-slate-900/50">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/20 text-rose-200 ring-1 ring-rose-400/30">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 3 2 9l10 6 10-6-10-6Zm0 0v6m0 12V9" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Space for Organizations</p>
                            <p class="text-lg font-semibold text-white">Featured allies</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach ($organizations as $organization)
                            <a href="{{ $organization['url'] }}" class="flex items-center justify-between rounded-xl bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/80">
                                <span>{{ $organization['name'] }}</span>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="m9 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </main>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    const componentId = @js($componentId);
    const sentinel = document.getElementById('events-sentinel');
    if (!sentinel) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const component = window.Livewire?.find(componentId);
                component?.call('loadMore');
            }
        });
    }, { rootMargin: '200px', threshold: 0 });

    observer.observe(sentinel);

    Livewire.hook('component.removed', ({ component }) => {
        if (component.id === componentId) {
            observer.disconnect();
        }
    });
});

document.addEventListener('livewire:init', () => {
    const componentId = @js($componentId);

    const setupEventObservers = () => {
        const cards = document.querySelectorAll('.event-observer');
        if (!cards.length) return;

        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && entry.target.dataset.eventDay) {
                    const year = Number(entry.target.dataset.eventYear);
                    const month = Number(entry.target.dataset.eventMonth);
                    const day = Number(entry.target.dataset.eventDay);
                    const component = window.Livewire?.find(componentId);
                    component?.call('setActiveDate', year, month, day);
                }
            });
        }, { threshold: 0.6, rootMargin: '0px 0px -20% 0px' });

        cards.forEach((card) => io.observe(card));

        Livewire.hook('component.removed', ({ component }) => {
            if (component.id === componentId) {
                io.disconnect();
            }
        });
    };

    setupEventObservers();
    Livewire.hook('message.processed', () => setupEventObservers());
});
</script>
