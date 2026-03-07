<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventType;
use Carbon\Carbon;
use Livewire\Component;

class SubmitEvent extends Component
{
    // Basic Event Information
    public string $eventName = '';
    public ?string $eventLink = null;
    public ?string $tagline = null;
    public ?string $description = null;
    public ?string $coverPic = null;

    // Category and Type
    public ?string $eventCategoryId = null;
    public ?string $eventTypeId = null;

    // Cost
    public ?string $currency = 'INR';
    public ?string $costDonation = null;

    // Date and Time
    public ?string $startDate = null;
    public ?string $startTime = null;
    public ?string $startAmPm = 'AM';
    public ?string $endDate = null;
    public ?string $endTime = null;
    public ?string $endAmPm = 'PM';
    public ?string $timeZone = 'Asia/Kolkata';

    // Location
    public ?string $locationName = null;
    public ?string $country = 'India';
    public ?string $stateProvince = null;
    public ?string $cityTown = null;
    public ?string $addressLine1 = null;
    public ?string $addressLine2 = null;
    public ?string $landmark = null;

    // Visibility and RSVP
    public ?string $eventVisibility = 'Public';
    public ?string $eventRsvp = 'Open';
    public ?string $rsvpVisibility = 'ATTENDEE_CHOICE';

    public array $categories = [];
    public array $types = [];

    public bool $submitted = false;

    public function mount(): void
    {
        $this->loadCategories();
        $this->loadTypes();
    }

    public function submit(): void
    {
        $this->validate([
            'eventName' => 'required|string|max:100',
            'eventCategoryId' => 'nullable|exists:event_categories,EVENT_CATEGORY_ID',
            'eventTypeId' => 'nullable|exists:event_types,EVENT_TYPE_ID',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'startTime' => 'nullable|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i',
            'eventLink' => 'nullable|url|max:100',
            'coverPic' => 'nullable|url|max:2048',
            'tagline' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'costDonation' => 'nullable|string|max:50',
            'locationName' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'stateProvince' => 'nullable|string|max:100',
            'cityTown' => 'nullable|string|max:100',
            'addressLine1' => 'nullable|string',
            'addressLine2' => 'nullable|string',
            'landmark' => 'nullable|string',
            'timeZone' => 'nullable|string|max:100',
        ]);

        // Convert time format if needed
        $startTimeFormatted = $this->startTime ? Carbon::createFromFormat('H:i', $this->startTime)->format('H:i:s') : null;
        $endTimeFormatted = $this->endTime ? Carbon::createFromFormat('H:i', $this->endTime)->format('H:i:s') : null;

        Event::create([
            'EVENT_NAME' => $this->eventName,
            'EVENT_LINK' => $this->eventLink,
            'COVER_PIC' => $this->coverPic,
            'TAGLINE' => $this->tagline,
            'DESCRIPTION' => $this->description,
            'EVENT_CATEGORY_ID' => $this->eventCategoryId,
            'CURRENCY' => $this->currency,
            'COST_DONATION' => $this->costDonation,
            'START_DATE' => $this->startDate,
            'START_TIME' => $startTimeFormatted,
            'START_AM_PM' => $this->startAmPm,
            'END_DATE' => $this->endDate,
            'END_TIME' => $endTimeFormatted,
            'END_AM_PM' => $this->endAmPm,
            'TIME_ZONE' => $this->timeZone,
            'EVENT_TYPE_ID' => $this->eventTypeId,
            'LOCATION_NAME' => $this->locationName,
            'COUNTRY' => $this->country,
            'STATE_PROVINCE' => $this->stateProvince,
            'CITY_TOWN' => $this->cityTown,
            'ADDRESS_LINE_1' => $this->addressLine1,
            'ADDRESS_LINE_2' => $this->addressLine2,
            'LANDMARK' => $this->landmark,
            'EVENT_VISIBILITY' => $this->eventVisibility,
            'EVENT_RSVP' => $this->eventRsvp,
            'RSVP_VISIBILITY' => $this->rsvpVisibility,
            'RSVP_COUNT' => 0,
            'APPROVAL_STATUS' => 'pending',
        ]);

        $this->submitted = true;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->eventName = '';
        $this->eventLink = null;
        $this->tagline = null;
        $this->description = null;
        $this->coverPic = null;
        $this->eventCategoryId = null;
        $this->eventTypeId = null;
        $this->currency = 'INR';
        $this->costDonation = null;
        $this->startDate = null;
        $this->startTime = null;
        $this->startAmPm = 'AM';
        $this->endDate = null;
        $this->endTime = null;
        $this->endAmPm = 'PM';
        $this->timeZone = 'Asia/Kolkata';
        $this->locationName = null;
        $this->country = 'India';
        $this->stateProvince = null;
        $this->cityTown = null;
        $this->addressLine1 = null;
        $this->addressLine2 = null;
        $this->landmark = null;
        $this->eventVisibility = 'Public';
        $this->eventRsvp = 'Open';
        $this->rsvpVisibility = 'ATTENDEE_CHOICE';
    }

    protected function loadCategories(): void
    {
        $this->categories = EventCategory::query()
            ->orderBy('EVENT_CATEGORY_ID')
            ->get()
            ->mapWithKeys(function ($category) {
                return [$category->EVENT_CATEGORY_ID => $category->EVENT_CATEGORY_DESCRIPTION ?: $category->EVENT_CATEGORY_VALUE];
            })
            ->toArray();
    }

    protected function loadTypes(): void
    {
        $this->types = EventType::query()
            ->orderBy('EVENT_TYPE_ID')
            ->get()
            ->mapWithKeys(function ($type) {
                return [$type->EVENT_TYPE_ID => $type->EVENT_TYPE_DESCRIPTION ?: $type->EVENT_TYPE_VALUE];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.submit-event')
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Submit Event · Bahujan Collective',
            ]);
    }
}

