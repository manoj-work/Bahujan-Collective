<?php

namespace App\Support;

use App\Models\Event;
use Carbon\Carbon;

class EventMapper
{
    public static function toList(Event $event): array
    {
        $startDate = $event->START_DATE ? Carbon::parse($event->START_DATE) : null;
        $startTime = $event->START_TIME ? Carbon::parse($event->START_TIME) : null;
        $displayDate = $startDate
            ? $startDate->format('D, M d') . ($startTime ? ' • ' . $startTime->format('g:i A') : '')
            : 'Date TBA';
        $displayTime = $startTime ? $startTime->format('g:i A') : null;

        $city = trim((string) ($event->CITY_TOWN ?? ''));
        $location = $city !== '' ? $city : 'Online';

        $categoryValue = $event->category?->EVENT_CATEGORY_VALUE;
        $modeValue = $event->type?->EVENT_TYPE_VALUE;

        $categoryKey = self::normalizeKey($categoryValue ?? 'other');
        $modeKey = self::normalizeKey($modeValue ?? 'online');

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
            'distance' => 0,
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

    public static function toDetail(Event $event): array
    {
        $summary = self::toList($event);

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
                ?: 'Event',
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

    public static function normalizeKey(?string $value): string
    {
        return strtolower(str_replace([' ', '-'], '_', $value ?? ''));
    }
}
