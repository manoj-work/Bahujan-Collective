<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use App\Models\Organization;
use App\Support\EventMapper;
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

    public bool $filterOrganizationsByLocation = false;

    public bool $calendarBooted = false;

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
    }

    public function setMode(string $mode): void
    {
        $this->filters['mode'] = $mode;
        $this->syncCalendarEvents();
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
        $this->syncCalendarEvents();
    }

    public function setSelectedDate(int $year, int $month, int $day): void
    {
        $isSameDate = $this->selectedDate
            && (int) $this->selectedDate['year'] === $year
            && (int) $this->selectedDate['month'] === $month
            && (int) $this->selectedDate['day'] === $day;

        if ($isSameDate) {
            $this->selectedDate = null;
        } else {
            $this->selectedDate = compact('year', 'month', 'day');
            $this->setActiveDate($year, $month, $day);
        }

        $this->perPage = 4;
        $this->dispatch('calendar-date-selected', date: $this->selectedDateValue());
    }

    public function clearSelectedDate(): void
    {
        $this->selectedDate = null;
        $this->perPage = 4;
        $this->dispatch('calendar-date-selected', date: '');
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
        $this->syncCalendarEvents();
    }

    public function updatedLocation(): void
    {
        $this->perPage = 4;
        if ($this->sanitizeSearchTerm($this->location) === '') {
            $this->filterOrganizationsByLocation = false;
        }
        $this->syncCalendarEvents();
    }

    public function loadMore(): void
    {
        if ($this->perPage >= count($this->filteredEvents)) {
            $this->skipRender();
            return;
        }

        $this->perPage += 4;
    }

    public function getFilteredEventsProperty(): array
    {
        return $this->eventsQuery(true)
            ->orderByRaw('CASE WHEN START_DATE IS NULL THEN 1 ELSE 0 END')
            ->orderBy('START_DATE')
            ->orderBy('START_TIME')
            ->get()
            ->map(fn (Event $event) => EventMapper::toList($event))
            ->values()
            ->all();
    }

    /**
     * FullCalendar event feed: dated, approved events (not limited by pagination).
     */
    public function getCalendarEventsProperty(): array
    {
        return $this->eventsQuery(false)
            ->whereNotNull('START_DATE')
            ->orderBy('START_DATE')
            ->orderBy('START_TIME')
            ->get()
            ->map(function (Event $event) {
                $startDate = $event->START_DATE?->format('Y-m-d');
                $startTime = $event->START_TIME?->format('H:i:s');
                $endDate = $event->END_DATE?->format('Y-m-d') ?: $startDate;
                $endTime = $event->END_TIME?->format('H:i:s');

                $payload = [
                    'id' => (string) $event->EVENT_ID,
                    'title' => $event->EVENT_NAME,
                    'start' => $startTime ? "{$startDate}T{$startTime}" : $startDate,
                    'allDay' => $startTime === null,
                ];

                if ($endTime) {
                    $payload['end'] = "{$endDate}T{$endTime}";
                }

                return $payload;
            })
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

        $calendarEvents = [];
        if (! $this->calendarBooted) {
            $calendarEvents = $this->calendarEvents;
            $this->calendarBooted = true;
        }

        return view('livewire.home-page', [
            'filteredEvents' => $filteredEvents,
            'visibleEvents' => $visibleEvents,
            'eventSections' => $eventSections,
            'calendarEvents' => $calendarEvents,
            'organizations' => $this->loadOrganizations(),
        ])
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Bahujan Collective · Community Events',
            ]);
    }

    protected function loadCategories(): array
    {
        $categories = EventCategory::query()
            ->orderBy('EVENT_CATEGORY_ID')
            ->get()
            ->mapWithKeys(function (EventCategory $category) {
                $key = EventMapper::normalizeKey($category->EVENT_CATEGORY_VALUE);
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
                $key = EventMapper::normalizeKey($type->EVENT_TYPE_VALUE);
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

    protected function sanitizeSearchTerm(?string $value): string
    {
        return Str::limit(trim($value ?? ''), 100, '');
    }

    protected function eventsQuery(bool $applyListDateRules = true): Builder
    {
        $query = Event::query()
            ->with(['category', 'type'])
            ->where(function ($q) {
                $q->where('APPROVAL_STATUS', 'approved')
                    ->orWhereNull('APPROVAL_STATUS');
            });

        if ($applyListDateRules) {
            if ($this->selectedDate) {
                $query->whereDate('START_DATE', Carbon::create(
                    $this->selectedDate['year'],
                    $this->selectedDate['month'],
                    $this->selectedDate['day']
                ));
            } else {
                $query->upcoming();
            }
        }

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

        return $query;
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
        $query = Organization::query()->orderBy('ORG_NAME');

        $location = $this->sanitizeSearchTerm($this->location);
        if ($this->filterOrganizationsByLocation && $location !== '') {
            $query->where(function ($q) use ($location) {
                $this->applyLocationSearch($q, $location);
            });
        }

        return $query
            ->get()
            ->map(function (Organization $organization) {
                return [
                    'name' => $organization->ORG_NAME,
                    'url' => $organization->WEBSITE ?: '#',
                    'city' => $organization->CITY_TOWN,
                ];
            })
            ->toArray();
    }

    protected function selectedDateValue(): string
    {
        if (! $this->selectedDate) {
            return '';
        }

        return sprintf(
            '%04d-%02d-%02d',
            $this->selectedDate['year'],
            $this->selectedDate['month'],
            $this->selectedDate['day']
        );
    }

    protected function syncCalendarEvents(): void
    {
        $this->dispatch('calendar-set-events', events: $this->calendarEvents);
        $this->dispatch('calendar-date-selected', date: $this->selectedDateValue());
    }
}
