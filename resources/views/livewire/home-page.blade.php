@php $distanceOptions = [10, 25, 50, 100]; @endphp

@php $eventSections = $eventSections ?? []; @endphp

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

<div>
<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-[120px]"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-cyan-400/10 blur-[120px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-2 sm:px-6 lg:px-8 space-y-8">
        <main class="grid items-start gap-6 lg:grid-cols-3">
            <section class="lg:col-span-2 lg:max-h-[calc(100vh-12rem)] lg:overflow-y-auto lg:pr-2">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 backdrop-blur shadow-xl shadow-slate-900/50">
                    <div class="rounded-t-2xl border-b border-slate-800/70 bg-slate-900/95">
                        <div class="flex flex-col gap-4 border-b border-slate-800/60 px-6 py-5">
                            <div class="flex flex-wrap items-center justify-end gap-3 text-sm">
                                <div class="relative flex items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80 w-full sm:w-72">
                                    <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 5.64 5.64a7.5 7.5 0 0 0 11.02 11.02Z" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <input
                                        wire:model.live.debounce.300ms="search"
                                        class="bg-transparent text-sm placeholder:text-slate-500 focus:outline-none flex-1"
                                        placeholder="Search events by title"
                                        type="search"
                                        aria-label="Search events"
                                    />
                                </div>
                                <div class="relative flex items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80 w-full sm:w-64">
                                    <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z" stroke-linecap="round" stroke-linejoin="round" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                    <input
                                        wire:model.live.debounce.300ms="location"
                                        class="bg-transparent text-sm placeholder:text-slate-500 focus:outline-none flex-1"
                                        placeholder="Location, city, address"
                                        type="search"
                                        aria-label="Search by location"
                                    />
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Events</p>
                                <p class="text-xl font-semibold text-white">Find community gatherings near you</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 border-b border-slate-800/60 px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200 ring-1 ring-indigo-400/40">Event Type</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($modeOptions as $key => $label)
                                    <button
                                        type="button"
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
                                    type="button"
                                    wire:click="setCategory('all')"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['category'] === 'all' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                    All
                                </button>
                                @foreach ($categories as $key => $label)
                                    <button
                                        type="button"
                                        wire:click="setCategory('{{ $key }}')"
                                        class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['category'] === $key ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 px-6 py-6">
                        @forelse ($eventSections as $section)
                            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 shadow-inner shadow-slate-900/40" wire:key="section-{{ $section['label'] }}">
                                <div class="flex items-center justify-between border-b border-slate-800/60 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Date: {{ $section['label'] }}</p>
                                    <div class="flex items-center gap-2 text-xs text-slate-400">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                                        {{ count($section['events']) }} {{ count($section['events']) === 1 ? 'event' : 'events' }}
                                    </div>
                                </div>
                                <div class="divide-y divide-slate-800/60">
                                    @foreach ($section['events'] as $event)
                                        <div
                                            wire:key="event-{{ $event['id'] }}"
                                            wire:click="openEventDetail({{ $event['id'] }})"
                                            role="button"
                                            tabindex="0"
                                            class="flex w-full cursor-pointer flex-col gap-4 px-4 py-4 text-left transition hover:bg-slate-800/40 sm:flex-row sm:items-center sm:gap-6 event-observer"
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
                                                <p class="text-lg font-semibold sm:text-xl event-title">{{ $event['title'] }}</p>
                                                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <path d="M21 10H3m18 0v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9m18 0V7a2 2 0 0 0-2-2h-1m-2.5 0h-5M7 5H6a2 2 0 0 0-2 2v3m14-5V3m0 2H7m0-2v2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <span>{{ $event['time'] ?? 'Time TBA' }}</span>
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
                                            <div class="flex items-center self-end text-slate-400 sm:self-center">
                                                <span class="text-xs font-semibold uppercase tracking-wide">View</span>
                                                <svg class="ml-1 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="m9 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
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
                                    type="button"
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
                </div>
            </section>

            <aside class="space-y-4 lg:col-span-1 lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 p-5 shadow-xl shadow-slate-900/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Calendar</p>
                            <p class="text-lg font-semibold text-white">{{ $calendarTitle }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-slate-300">
                            <button type="button" class="rounded-lg bg-slate-800/80 px-2 py-1 ring-1 ring-slate-700/80 hover:bg-slate-700/80">‹</button>
                            <button type="button" class="rounded-lg bg-slate-800/80 px-2 py-1 ring-1 ring-slate-700/80 hover:bg-slate-700/80">›</button>
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
                                type="button"
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

@if ($showEventModal && $selectedEvent)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="event-detail-title">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" wire:click="closeEventDetail"></div>

        <div class="relative z-[101] flex max-h-[min(92vh,880px)] w-full max-w-2xl flex-col overflow-hidden rounded-3xl border border-slate-700/60 bg-slate-900 shadow-2xl shadow-black/40">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-slate-800/60 px-6 py-5 sm:px-8">
                <div class="min-w-0 space-y-1.5">
                    <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-slate-400">Event details</p>
                    <h3 id="event-detail-title" class="text-xl font-semibold leading-snug text-white sm:text-2xl">
                        {{ $selectedEvent['title'] }}
                    </h3>
                    @if (! empty($selectedEvent['tagline']))
                        <p class="text-sm leading-relaxed text-slate-300">{{ $selectedEvent['tagline'] }}</p>
                    @endif
                </div>
                <button
                    type="button"
                    wire:click="closeEventDetail"
                    class="mt-0.5 shrink-0 rounded-full p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white"
                    aria-label="Close event details"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5 sm:space-y-6 sm:px-8 sm:py-6">
                @if (! empty($selectedEvent['cover_pic']))
                    <div class="overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-950">
                        <img
                            src="{{ $selectedEvent['cover_pic'] }}"
                            alt="{{ $selectedEvent['title'] }}"
                            class="h-44 w-full object-cover sm:h-52"
                            loading="lazy"
                        >
                    </div>
                @endif

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-slate-800 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-slate-200 ring-1 ring-slate-700/80">
                        {{ $selectedEvent['category_label'] }}
                    </span>
                    <span class="rounded-full bg-indigo-500/15 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-indigo-100 ring-1 ring-indigo-400/30">
                        {{ $selectedEvent['mode_label'] }}
                    </span>
                    @if (! empty($selectedEvent['visibility']))
                        <span class="rounded-full bg-cyan-500/10 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-cyan-100 ring-1 ring-cyan-400/25">
                            {{ $selectedEvent['visibility'] }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1 rounded-2xl bg-slate-800/50 px-5 py-4 ring-1 ring-slate-700/50">
                        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-slate-400">When</p>
                        <div class="mt-3 space-y-1.5">
                            <p class="text-[15px] font-semibold text-white">{{ $selectedEvent['start_date_label'] }}</p>
                            <p class="text-sm text-slate-300">
                                {{ $selectedEvent['time'] ?? 'Time TBA' }}
                                @if (! empty($selectedEvent['end_time']))
                                    – {{ $selectedEvent['end_time'] }}
                                @endif
                            </p>
                            @if (! empty($selectedEvent['timezone']))
                                <p class="text-xs text-slate-400">{{ $selectedEvent['timezone'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 rounded-2xl bg-slate-800/50 px-5 py-4 ring-1 ring-slate-700/50">
                        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-slate-400">Where</p>
                        <div class="mt-3 space-y-1.5">
                            <p class="text-[15px] font-semibold text-white">{{ $selectedEvent['location_name'] ?: $selectedEvent['location'] }}</p>
                            @if (! empty($selectedEvent['address_lines']))
                                <p class="text-sm leading-relaxed text-slate-300">{{ implode(', ', $selectedEvent['address_lines']) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if (! empty($selectedEvent['description']))
                    <div class="rounded-2xl bg-slate-800/30 px-5 py-4 ring-1 ring-slate-700/40">
                        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-slate-400">About</p>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-200">{{ $selectedEvent['description'] }}</p>
                    </div>
                @endif

                <div class="flex gap-3">
                    <div class="min-w-0 flex-1 rounded-2xl bg-slate-800/40 px-3 py-4 text-center ring-1 ring-slate-700/45 sm:px-5 sm:text-left">
                        <p class="text-[10px] font-medium uppercase tracking-[0.16em] text-slate-400 sm:text-[11px]">RSVPs</p>
                        <p class="mt-2 text-base font-semibold text-white sm:text-lg">{{ $selectedEvent['rsvps'] }}</p>
                    </div>
                    <div class="min-w-0 flex-1 rounded-2xl bg-slate-800/40 px-3 py-4 text-center ring-1 ring-slate-700/45 sm:px-5 sm:text-left">
                        <p class="text-[10px] font-medium uppercase tracking-[0.16em] text-slate-400 sm:text-[11px]">Cost</p>
                        <p class="mt-2 text-base font-semibold text-white sm:text-lg">{{ $selectedEvent['cost'] ?: 'Free' }}</p>
                    </div>
                    <div class="min-w-0 flex-1 rounded-2xl bg-slate-800/40 px-3 py-4 text-center ring-1 ring-slate-700/45 sm:px-5 sm:text-left">
                        <p class="text-[10px] font-medium uppercase tracking-[0.16em] text-slate-400 sm:text-[11px]">RSVP</p>
                        <p class="mt-2 text-base font-semibold text-white sm:text-lg">{{ $selectedEvent['rsvp_status'] ?: 'Open' }}</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col-reverse gap-3 border-t border-slate-800/70 bg-slate-950/40 px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-8 sm:py-5">
                <button
                    type="button"
                    wire:click="closeEventDetail"
                    class="inline-flex items-center justify-center rounded-full bg-slate-800 px-6 py-2.5 text-sm font-medium text-slate-200 ring-1 ring-slate-700 transition hover:bg-slate-700"
                >
                    Close
                </button>
                @if (! empty($selectedEvent['event_link']))
                    <a
                        href="{{ $selectedEvent['event_link'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400"
                    >
                        Open event link
                    </a>
                @else
                    <span class="hidden sm:block"></span>
                @endif
            </div>
        </div>
    </div>
@endif

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
</div>
