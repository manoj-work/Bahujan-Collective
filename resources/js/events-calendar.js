import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

let calendar = null;
let selectedDate = '';
let eventsSignature = '';

function parseEvents(raw) {
    if (Array.isArray(raw)) {
        return raw;
    }

    if (typeof raw === 'string') {
        try {
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    return [];
}

function dataElement() {
    return document.getElementById('events-calendar-data');
}

function homeComponent() {
    const componentId = dataElement()?.dataset.componentId;
    return componentId ? window.Livewire?.find(componentId) : null;
}

function formatLocalDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function highlightSelectedDate() {
    document.querySelectorAll('#events-calendar .fc-daygrid-day').forEach((day) => {
        day.classList.toggle('is-selected-day', day.getAttribute('data-date') === selectedDate);
    });
}

function setCalendarEvents(events) {
    if (!calendar) {
        return;
    }

    const nextSignature = JSON.stringify(events);
    if (nextSignature === eventsSignature) {
        return;
    }

    eventsSignature = nextSignature;
    calendar.removeAllEvents();
    calendar.addEventSource(events);
}

function initEventsCalendar() {
    const el = document.getElementById('events-calendar');
    const dataEl = dataElement();

    if (!el || !dataEl) {
        return;
    }

    const events = parseEvents(dataEl.dataset.events || '[]');
    selectedDate = dataEl.dataset.selectedDate || '';
    eventsSignature = JSON.stringify(events);

    if (calendar) {
        setCalendarEvents(events);
        highlightSelectedDate();
        return;
    }

    calendar = new Calendar(el, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next',
        },
        height: 'auto',
        expandRows: false,
        firstDay: 1,
        navLinks: false,
        displayEventTime: false,
        displayEventEnd: false,
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        eventDisplay: 'list-item',
        events,
        dateClick(info) {
            const [year, month, day] = info.dateStr.split('-').map(Number);
            homeComponent()?.call('setSelectedDate', year, month, day);
        },
        eventClick(info) {
            info.jsEvent.preventDefault();
            const eventId = Number(info.event.id);
            if (Number.isFinite(eventId) && eventId > 0) {
                window.Livewire.dispatch('open-event-detail', { eventId });
            }
        },
        dayCellClassNames(arg) {
            return formatLocalDate(arg.date) === selectedDate ? ['is-selected-day'] : [];
        },
        datesSet() {
            highlightSelectedDate();
        },
    });

    calendar.render();
}

document.addEventListener('livewire:init', () => {
    initEventsCalendar();

    Livewire.on('calendar-set-events', (payload) => {
        const events = parseEvents(payload?.events ?? payload?.[0]?.events ?? payload);
        if (calendar) {
            setCalendarEvents(events);
        }
    });

    Livewire.on('calendar-date-selected', (payload) => {
        selectedDate = payload?.date ?? payload?.[0]?.date ?? '';
        const dataEl = dataElement();
        if (dataEl) {
            dataEl.dataset.selectedDate = selectedDate;
        }
        highlightSelectedDate();
    });
});

document.addEventListener('livewire:navigated', initEventsCalendar);
