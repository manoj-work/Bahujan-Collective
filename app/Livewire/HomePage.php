<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;

class HomePage extends Component
{
    public int $perPage = 4;

    public array $filters = [
        'mode' => 'all',
        'distance' => 100,
        'category' => 'all',
    ];

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'location', except: '')]
    public string $location = '';

    public array $activeDate = [
        'year' => 0,
        'month' => 0,
        'day' => 0,
    ];

    public ?array $selectedDate = null;

    public array $categories = [];

    public array $modeOptions = [];

    public array $organizations = [];

    public bool $showEventModal = false;

    public ?array $selectedEvent = null;

    public function mount(): void
    {
        $today = Carbon::today();

        $this->activeDate = [
            'year' => $today->year,
            'month' => $today->month,
            'day' => $today->day,
        ];

        $this->selectedDate = null;

        $this->categories = $this->loadCategories();
        $this->modeOptions = $this->loadEventTypes();
        $this->organizations = $this->loadOrganizations();
    }

    public function setMode(string $mode): void
    {
        $this->filters['mode'] = $mode;
    }

    public function setDistance(int $distance): void
    {
        $this->filters['distance'] = $distance;
    }

    public function setCategory(string $category): void
    {
        $this->filters['category'] = $category;

        // Reset date filter when showing all categories so every event is visible.
        if ($category === 'all') {
            $this->selectedDate = null;
        }

        $this->perPage = 4;
    }

    public function setSelectedDate(int $year, int $month, int $day): void
    {
        $this->selectedDate = compact('year', 'month', 'day');
        $this->setActiveDate($year, $month, $day);
        $this->perPage = 4;
    }

    public function setActiveDate(int $year, int $month, int $day): void
    {
        $this->activeDate = compact('year', 'month', 'day');
    }

    public function updatedFilters(...$args): void
    {
        // Reset pagination when filters change.
        $this->perPage = 4;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 4;
    }

    public function updatedLocation(): void
    {
        $this->perPage = 4;
    }

    public function loadMore(): void
    {
        $this->perPage += 4;
    }

    public function openEventDetail(int $eventId): void
    {
        $event = Event::query()
            ->with(['category', 'type'])
            ->where(function ($q) {
                $q->where('APPROVAL_STATUS', 'approved')
                    ->orWhereNull('APPROVAL_STATUS');
            })
            ->where('EVENT_ID', $eventId)
            ->first();

        if (! $event) {
            return;
        }

        $this->selectedEvent = $this->mapEventDetail($event);
        $this->showEventModal = true;
    }

    public function closeEventDetail(): void
    {
        $this->showEventModal = false;
        $this->selectedEvent = null;
    }

    public function getFilteredEventsProperty(): array
    {
        $query = Event::query()
            ->with(['category', 'type'])
            ->where(function ($q) {
                $q->where('APPROVAL_STATUS', 'approved')
                    ->orWhereNull('APPROVAL_STATUS');
            })
            ->upcoming();

        if ($this->filters['category'] !== 'all') {
            $categoryFilter = $this->filters['category'];
            $query->whereHas('category', function ($q) use ($categoryFilter) {
                $column = $q->getQuery()->grammar->wrap('EVENT_CATEGORY_VALUE');
                $q->whereRaw(
                    "LOWER(REPLACE(REPLACE({$column}, ' ', '_'), '-', '_')) = ?",
                    [$categoryFilter]
                );
            });
        }

        if ($this->filters['mode'] !== 'all') {
            $modeFilter = $this->filters['mode'] === 'virtual' ? 'online' : $this->filters['mode'];
            $query->whereHas('type', function ($q) use ($modeFilter) {
                $column = $q->getQuery()->grammar->wrap('EVENT_TYPE_VALUE');
                $q->whereRaw(
                    "LOWER(REPLACE(REPLACE({$column}, ' ', '_'), '-', '_')) = ?",
                    [$modeFilter]
                );
            });
        }

        $search = $this->sanitizeSearchTerm($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereLike('EVENT_NAME', '%' . $search . '%')
                    ->orWhere(function ($locationQuery) use ($search) {
                        $this->applyLocationSearch($locationQuery, $search);
                    });
            });
        }

        $location = $this->sanitizeSearchTerm($this->location);
        if ($location !== '') {
            $query->where(function ($q) use ($location) {
                $this->applyLocationSearch($q, $location);
            });
        }

        if ($this->selectedDate) {
            $query->whereDate('START_DATE', Carbon::create(
                $this->selectedDate['year'],
                $this->selectedDate['month'],
                $this->selectedDate['day']
            ));
        }

        return $query
            ->orderByRaw('CASE WHEN START_DATE IS NULL THEN 1 ELSE 0 END')
            ->orderBy('START_DATE')
            ->orderBy('START_TIME')
            ->get()
            ->map(fn (Event $event) => $this->mapEvent($event))
            ->values()
            ->all();
    }

    public function getVisibleEventsProperty(): array
    {
        return array_slice($this->filteredEvents, 0, $this->perPage);
    }

    public function render()
    {
        $filteredEvents = $this->filteredEvents;
        $visibleEvents = $this->visibleEvents;

        // Sections in chronological date order, with Date TBA after dated events.
        $eventSections = collect($visibleEvents)
            ->groupBy('date_key')
            ->map(fn ($events, $label) => [
                'label' => $events->first()['date_heading'] ?? $label,
                'order' => $events->first()['date_order'] ?? PHP_INT_MAX,
                'events' => $events->sortBy('time_sort')->values()->all(),
            ])
            ->sortBy('order')
            ->values()
            ->all();

        return view('livewire.home-page', [
            'filteredEvents' => $filteredEvents,
            'visibleEvents' => $visibleEvents,
            'eventSections' => $eventSections,
        ])
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Bahujan Collective · Community Events',
            ]);
    }

    protected function mapEvent(Event $event): array
    {
        $startDate = $event->START_DATE ? Carbon::parse($event->START_DATE) : null;
        $startTime = $event->START_TIME ? Carbon::parse($event->START_TIME) : null;
        $displayDate = $startDate
            ? $startDate->format('D, M d') . ($startTime ? ' • ' . $startTime->format('g:i A') : '')
            : 'Date TBA';
        $displayTime = $startTime ? $startTime->format('g:i A') : null;

        $location = $event->LOCATION_NAME
            ?? collect([$event->ADDRESS_LINE_1, $event->ADDRESS_LINE_2, $event->CITY_TOWN, $event->STATE_PROVINCE])
                ->filter()
                ->implode(', ');

        $categoryValue = $event->category?->EVENT_CATEGORY_VALUE;
        $modeValue = $event->type?->EVENT_TYPE_VALUE;

        $categoryKey = $this->normalizeKey($categoryValue ?? 'other');
        $modeKey = $this->normalizeKey($modeValue ?? 'online');

        $dateKey = $startDate ? $startDate->format('Y-m-d') : 'date_tba';
        $dateHeading = $startDate ? $startDate->format('d M Y') : 'TBA';
        $dateOrder = $startDate ? $startDate->getTimestamp() : PHP_INT_MAX;
        $timeSort = $startTime ? ((int) $startTime->format('H') * 60) + (int) $startTime->format('i') : PHP_INT_MAX;

        return [
            'id' => $event->EVENT_ID,
            'title' => $event->EVENT_NAME,
            'category' => $categoryKey,
            'date' => $displayDate,
            'time' => $displayTime,
            'location' => $location ?: 'Online',
            'mode' => $modeKey,
            'distance' => 0, // placeholder until geolocation/distance is added
            'rsvps' => $event->RSVP_COUNT ?? 0,
            'calendar_year' => $startDate?->year,
            'calendar_month' => $startDate?->month,
            'calendar_day' => $startDate?->day,
            'date_key' => $dateKey,
            'date_heading' => $dateHeading,
            'date_order' => $dateOrder,
            'time_sort' => $timeSort,
        ];
    }

    protected function mapEventDetail(Event $event): array
    {
        $summary = $this->mapEvent($event);

        $endDate = $event->END_DATE ? Carbon::parse($event->END_DATE) : null;
        $endTime = $event->END_TIME ? Carbon::parse($event->END_TIME) : null;

        $addressParts = collect([
            $event->ADDRESS_LINE_1,
            $event->ADDRESS_LINE_2,
            $event->LANDMARK,
            $event->CITY_TOWN,
            $event->STATE_PROVINCE,
            $event->COUNTRY,
        ])
            ->map(fn ($part) => is_string($part) ? trim($part) : $part)
            ->filter()
            ->unique(fn ($part) => strtolower((string) $part))
            ->values()
            ->all();

        $donation = trim((string) ($event->COST_DONATION ?? ''));
        $currency = trim((string) ($event->CURRENCY ?? ''));
        if ($donation === '' || $donation === '0' || strcasecmp($donation, 'free') === 0) {
            $cost = 'Free';
        } elseif ($currency !== '') {
            $cost = $currency . ' ' . $donation;
        } else {
            $cost = $donation;
        }

        return array_merge($summary, [
            'tagline' => $event->TAGLINE,
            'description' => $event->DESCRIPTION,
            'cover_pic' => $event->COVER_PIC,
            'event_link' => $event->EVENT_LINK,
            'category_label' => $event->category?->EVENT_CATEGORY_DESCRIPTION
                ?: $event->category?->EVENT_CATEGORY_VALUE
                ?: ($this->categories[$summary['category']] ?? 'Event'),
            'mode_label' => $event->type?->EVENT_TYPE_DESCRIPTION
                ?: $event->type?->EVENT_TYPE_VALUE
                ?: str_replace('_', ' ', ucfirst($summary['mode'])),
            'start_date_label' => $summary['date_heading'],
            'end_date_label' => $endDate ? $endDate->format('d M Y') : null,
            'end_time' => $endTime ? $endTime->format('g:i A') : null,
            'timezone' => $event->TIME_ZONE,
            'location_name' => $event->LOCATION_NAME,
            'address_lines' => $addressParts,
            'cost' => $cost !== '' ? $cost : null,
            'visibility' => $event->EVENT_VISIBILITY,
            'rsvp_status' => $event->EVENT_RSVP,
        ]);
    }

    protected function loadCategories(): array
    {
        $categories = EventCategory::query()
            ->orderBy('EVENT_CATEGORY_ID')
            ->get()
            ->mapWithKeys(function (EventCategory $category) {
                $key = $this->normalizeKey($category->EVENT_CATEGORY_VALUE);
                $label = $category->EVENT_CATEGORY_DESCRIPTION ?: $category->EVENT_CATEGORY_VALUE;
                return [$key => $label];
            })
            ->toArray();

        if (empty($categories)) {
            return [
                'class' => 'Class',
                'protest' => 'Protest',
                'reading_session' => 'Reading Session',
                'wellness_session' => 'Wellness Session',
                'celebration' => 'Celebration',
                'panel' => 'Panel',
                'book_release' => 'Book Release',
                'art_museum_display' => 'Art Museum Display',
                'talk_series' => 'Talk Series',
                'other' => 'Other',
            ];
        }

        return $categories;
    }

    protected function loadEventTypes(): array
    {
        $types = EventType::query()
            ->orderBy('EVENT_TYPE_ID')
            ->get()
            ->mapWithKeys(function (EventType $type) {
                $key = $this->normalizeKey($type->EVENT_TYPE_VALUE);
                $label = $type->EVENT_TYPE_DESCRIPTION ?: $type->EVENT_TYPE_VALUE;
                return [$key => $label];
            })
            ->toArray();

        if (empty($types)) {
            return [
                'in_person' => 'In Person',
                'online' => 'Online',
                'hybrid' => 'Hybrid',
                'virtual' => 'Virtual',
                'all' => 'All',
            ];
        }

        // Ensure "all" option exists for UI filter.
        $types['all'] = 'All';

        return $types;
    }

    protected function normalizeKey(?string $value): string
    {
        return strtolower(str_replace([' ', '-'], '_', $value ?? ''));
    }

    protected function sanitizeSearchTerm(?string $value): string
    {
        return Str::limit(trim($value ?? ''), 100, '');
    }

    protected function applyLocationSearch(Builder $query, string $search): void
    {
        $query->whereLike('LOCATION_NAME', '%' . $search . '%')
            ->orWhereLike('CITY_TOWN', '%' . $search . '%')
            ->orWhereLike('STATE_PROVINCE', '%' . $search . '%')
            ->orWhereLike('COUNTRY', '%' . $search . '%')
            ->orWhereLike('ADDRESS_LINE_1', '%' . $search . '%')
            ->orWhereLike('ADDRESS_LINE_2', '%' . $search . '%')
            ->orWhereLike('LANDMARK', '%' . $search . '%');
    }

    protected function loadOrganizations(): array
    {
        return Organization::query()
            ->orderBy('ORG_NAME')
            ->get()
            ->map(function (Organization $organization) {
                return [
                    'name' => $organization->ORG_NAME,
                    'url' => $organization->WEBSITE ?: '#',
                ];
            })
            ->toArray();
    }
}
