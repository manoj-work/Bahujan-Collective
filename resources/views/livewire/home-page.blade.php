@php $distanceOptions = [10, 25, 50, 100]; @endphp

@php $eventSections = $eventSections ?? []; @endphp

@php
    $hasMore = count($filteredEvents ?? []) > count($visibleEvents ?? []);
    $componentId = $this->getId();
    $calendarEvents = $calendarEvents ?? [];
    $selectedDateValue = $selectedDate
        ? sprintf('%04d-%02d-%02d', $selectedDate['year'], $selectedDate['month'], $selectedDate['day'])
        : '';
@endphp

<div>
<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-[120px]"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-cyan-400/10 blur-[120px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-10 pt-2 sm:px-6 lg:px-8">
        <main class="grid items-start gap-6 lg:grid-cols-3">
            <section class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 shadow-xl shadow-slate-900/50 backdrop-blur">
                    <div class="space-y-4 border-b border-slate-800/70 px-6 py-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative flex flex-1 items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80">
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 5.64 5.64a7.5 7.5 0 0 0 11.02 11.02Z" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input
                                    wire:model.live.debounce.300ms="search"
                                    class="w-full bg-transparent text-sm placeholder:text-slate-500 focus:outline-none"
                                    placeholder="Search events by title"
                                    type="search"
                                    aria-label="Search events"
                                />
                            </div>
                            <div class="flex flex-1 flex-col gap-2" id="location-search-root" data-component-id="{{ $componentId }}">
                                <div class="relative">
                                    <div class="relative flex items-center gap-2 rounded-full bg-slate-800/80 px-4 py-2 text-slate-200 ring-1 ring-slate-700/80">
                                        <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z" stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>
                                        <input
                                            id="location-search"
                                            value="{{ $location }}"
                                            class="w-full bg-transparent text-sm placeholder:text-slate-500 focus:outline-none"
                                            placeholder="Search a city or place"
                                            type="search"
                                            autocomplete="off"
                                            aria-label="Search by location"
                                            aria-autocomplete="list"
                                            aria-controls="location-search-results"
                                        />
                                    </div>
                                    <ul
                                        id="location-search-results"
                                        class="absolute z-30 mt-2 hidden w-full overflow-hidden rounded-2xl border border-slate-700/80 bg-slate-900 py-1 shadow-2xl shadow-slate-950/70"
                                        role="listbox"
                                    ></ul>
                                </div>
                                @if (trim($location) !== '')
                                    <label class="flex cursor-pointer items-center gap-2 px-1 text-xs text-slate-300">
                                        <input
                                            type="checkbox"
                                            wire:model.live="filterOrganizationsByLocation"
                                            class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-cyan-400 focus:ring-cyan-400/40"
                                        >
                                        <span>Also match organizations to this location</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Events</p>
                            <p class="text-xl font-semibold text-white">
                                @if ($selectedDate)
                                    {{ \Carbon\Carbon::create($selectedDate['year'], $selectedDate['month'], $selectedDate['day'])->format('D, d M Y') }}
                                @else
                                    Find community gatherings near you
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200 ring-1 ring-indigo-400/40">Event Type</span>
                            @foreach ($modeOptions as $key => $label)
                                <button
                                    type="button"
                                    wire:click="setMode('{{ $key }}')"
                                    class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $filters['mode'] === $key ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/40' : 'bg-slate-800/80 text-slate-200 ring-1 ring-slate-700/80 hover:bg-slate-700/70' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Categories</span>
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

                    <div class="space-y-5 px-6 py-6">
                        @forelse ($eventSections as $section)
                            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60" wire:key="section-{{ $section['label'] }}">
                                <div class="flex items-center justify-between border-b border-slate-800/60 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Date: {{ $section['label'] }}</p>
                                    <p class="text-xs text-slate-400">{{ count($section['events']) }} {{ count($section['events']) === 1 ? 'event' : 'events' }}</p>
                                </div>
                                <div class="divide-y divide-slate-800/60">
                                    @foreach ($section['events'] as $event)
                                        <div
                                            wire:key="event-{{ $event['id'] }}"
                                            role="button"
                                            tabindex="0"
                                            x-on:click="window.Livewire.dispatch('open-event-detail', { eventId: {{ $event['id'] }} })"
                                            x-on:keydown.enter="window.Livewire.dispatch('open-event-detail', { eventId: {{ $event['id'] }} })"
                                            class="flex w-full cursor-pointer flex-col gap-3 px-4 py-4 text-left transition hover:bg-slate-800/40 sm:flex-row sm:items-center sm:justify-between"
                                        >
                                            <div class="space-y-2">
                                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                                                    <span class="rounded-full bg-slate-800/80 px-3 py-1 text-slate-200 ring-1 ring-slate-700/80">{{ $categories[$event['category']] ?? 'Event' }}</span>
                                                    <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-indigo-100 ring-1 ring-indigo-400/30">{{ str_replace('_', ' ', ucfirst($event['mode'])) }}</span>
                                                </div>
                                                <p class="text-lg font-semibold text-white">{{ $event['title'] }}</p>
                                                <p class="text-sm text-slate-300">{{ $event['time'] ?? 'Time TBA' }} · {{ $event['location'] }}</p>
                                            </div>
                                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">View</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-800/70 px-6 py-10 text-center text-slate-300">
                                No events match the selected filters yet. Try another mode or category.
                            </div>
                        @endforelse

                        <div class="flex items-center justify-center gap-3 pt-2">
                            @if ($hasMore)
                                <button
                                    type="button"
                                    wire:click="loadMore"
                                    class="rounded-full bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 ring-1 ring-slate-700/80 transition hover:bg-slate-700/80">
                                    Load more events
                                </button>
                            @endif
                        </div>
                        @if ($hasMore)
                            <div
                                id="events-sentinel"
                                class="h-8"
                                data-has-more="1"
                                data-component-id="{{ $componentId }}"
                                wire:intersect="loadMore"
                            ></div>
                        @endif
                    </div>
                </div>
            </section>

            <aside class="space-y-4 lg:col-span-1 lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/80 p-4 shadow-xl shadow-slate-900/50">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-indigo-300">Calendar</p>
                            <p class="text-sm font-semibold text-white">Events by date</p>
                        </div>
                        @if ($selectedDate)
                            <button
                                type="button"
                                wire:click="clearSelectedDate"
                                class="rounded-full bg-cyan-500/15 px-3 py-1 text-[11px] font-semibold text-cyan-100 ring-1 ring-cyan-400/30"
                            >
                                Show all
                            </button>
                        @endif
                    </div>

                    <div class="mini-calendar rounded-xl bg-slate-950/50 p-2" wire:ignore>
                        <div
                            id="events-calendar-data"
                            class="hidden"
                            data-component-id="{{ $componentId }}"
                            data-events='@json($calendarEvents)'
                            data-selected-date="{{ $selectedDateValue }}"
                        ></div>
                        <div id="events-calendar"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800/70 bg-gradient-to-br from-slate-900 via-slate-900/80 to-slate-950 p-5 shadow-xl shadow-slate-900/50">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Featured allies</p>
                    @if ($filterOrganizationsByLocation && trim($location) !== '')
                        <p class="mt-1 text-xs text-slate-400">Showing organizations in “{{ $location }}”</p>
                    @endif
                    <div class="mt-3 space-y-2">
                        @forelse ($organizations as $organization)
                            <a href="{{ $organization['url'] }}" class="flex items-center justify-between rounded-xl bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/80">
                                <span>{{ $organization['name'] }}</span>
                                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="m9 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        @empty
                            <p class="rounded-xl bg-slate-800/50 px-4 py-3 text-sm text-slate-400 ring-1 ring-slate-700/60">
                                No organizations match this location.
                            </p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </main>
    </div>
</div>

</div>
