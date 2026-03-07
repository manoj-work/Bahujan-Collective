<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use App\Models\Organization;
use Carbon\Carbon;
use Livewire\Component;

class HomePage extends Component
{
    public int $perPage = 4;

    public array $filters = [
        'mode' => 'all',
        'distance' => 100,
        'category' => 'all',
    ];

    public string $search = '';

    public array $activeDate = [
        'year' => 0,
        'month' => 0,
        'day' => 0,
    ];

    public ?array $selectedDate = null;

    public array $categories = [];

    public array $modeOptions = [];

    public array $organizations = [];

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

    public function loadMore(): void
    {
        $this->perPage += 4;
    }

    public function getFilteredEventsProperty(): array
    {
        $query = Event::query()
            ->with(['category', 'type'])
            ->where(function ($q) {
                $q->where('APPROVAL_STATUS', 'approved')
                    ->orWhereNull('APPROVAL_STATUS');
            })
            ->where(function ($q) {
                $q->whereDate('START_DATE', '>=', Carbon::today())
                    ->orWhereNull('START_DATE');
            });

        if ($this->filters['category'] !== 'all') {
            $categoryFilter = $this->filters['category'];
            $query->whereHas('category', function ($q) use ($categoryFilter) {
                $q->whereRaw(
                    'LOWER(REPLACE(REPLACE("EVENT_CATEGORY_VALUE", \' \', \'_\'), \'-\', \'_\')) = ?',
                    [$categoryFilter]
                );
            });
        }

        if ($this->filters['mode'] !== 'all') {
            $modeFilter = $this->filters['mode'] === 'virtual' ? 'online' : $this->filters['mode'];
            $query->whereHas('type', function ($q) use ($modeFilter) {
                $q->whereRaw(
                    'LOWER(REPLACE(REPLACE("EVENT_TYPE_VALUE", \' \', \'_\'), \'-\', \'_\')) = ?',
                    [$modeFilter]
                );
            });
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('EVENT_NAME', 'ILIKE', '%' . $search . '%')
                    ->orWhere('LOCATION_NAME', 'ILIKE', '%' . $search . '%');
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

        $groupedEvents = collect($visibleEvents)->groupBy('category')->all();

        return view('livewire.home-page', [
            'filteredEvents' => $filteredEvents,
            'visibleEvents' => $visibleEvents,
            'groupedEvents' => $groupedEvents,
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

        $location = $event->LOCATION_NAME
            ?? collect([$event->ADDRESS_LINE_1, $event->ADDRESS_LINE_2, $event->CITY_TOWN, $event->STATE_PROVINCE])
                ->filter()
                ->implode(', ');

        $categoryValue = $event->category?->EVENT_CATEGORY_VALUE;
        $modeValue = $event->type?->EVENT_TYPE_VALUE;

        $categoryKey = $this->normalizeKey($categoryValue ?? 'other');
        $modeKey = $this->normalizeKey($modeValue ?? 'online');

        return [
            'id' => $event->EVENT_ID,
            'title' => $event->EVENT_NAME,
            'category' => $categoryKey,
            'date' => $displayDate,
            'location' => $location ?: 'Online',
            'mode' => $modeKey,
            'distance' => 0, // placeholder until geolocation/distance is added
            'rsvps' => $event->RSVP_COUNT ?? 0,
            'calendar_year' => $startDate?->year,
            'calendar_month' => $startDate?->month,
            'calendar_day' => $startDate?->day,
        ];
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
