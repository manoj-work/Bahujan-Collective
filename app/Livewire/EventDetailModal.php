<?php

namespace App\Livewire;

use App\Models\Event;
use App\Support\EventMapper;
use Livewire\Attributes\On;
use Livewire\Component;

class EventDetailModal extends Component
{
    public bool $showEventModal = false;

    public ?array $selectedEvent = null;

    #[On('open-event-detail')]
    public function open(mixed $eventId = null): void
    {
        if (is_array($eventId)) {
            $eventId = $eventId['eventId'] ?? $eventId['event_id'] ?? 0;
        }

        $eventId = (int) $eventId;
        if ($eventId < 1) {
            return;
        }

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

        $this->selectedEvent = EventMapper::toDetail($event);
        $this->showEventModal = true;
    }

    #[On('close-event-detail')]
    public function close(): void
    {
        $this->showEventModal = false;
        $this->selectedEvent = null;
    }

    public function render()
    {
        return view('livewire.event-detail-modal');
    }
}
