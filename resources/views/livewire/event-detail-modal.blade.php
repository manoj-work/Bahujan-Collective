<div>
@if ($showEventModal && $selectedEvent)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="event-detail-title" wire:keydown.escape.window="close">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" wire:click="close"></div>

        <div class="relative z-[101] flex max-h-[min(92vh,880px)] w-full max-w-2xl flex-col overflow-hidden rounded-3xl border border-slate-700/60 bg-slate-900 shadow-2xl shadow-black/40">
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
                    wire:click="close"
                    class="mt-0.5 shrink-0 rounded-full p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white"
                    aria-label="Close event details"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

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

            <div class="flex flex-col-reverse gap-3 border-t border-slate-800/70 bg-slate-950/40 px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-8 sm:py-5">
                <button
                    type="button"
                    wire:click="close"
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
</div>
