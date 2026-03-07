<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Seed the events table with sample data.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // First, preserve existing events by moving any past events into the future.
        // We do this by adding years until START_DATE is >= today, keeping the
        // rest of the event data intact.
        $existingEvents = DB::table('events')
            ->select('EVENT_ID', 'START_DATE', 'END_DATE')
            ->whereNotNull('START_DATE')
            ->get();

        foreach ($existingEvents as $event) {
            $startDate = Carbon::parse($event->START_DATE);
            $endDate = $event->END_DATE ? Carbon::parse($event->END_DATE) : null;

            if ($startDate->lt($today)) {
                while ($startDate->lt($today)) {
                    $startDate->addYear();
                    if ($endDate) {
                        $endDate->addYear();
                    }
                }

                DB::table('events')
                    ->where('EVENT_ID', $event->EVENT_ID)
                    ->update([
                        'START_DATE' => $startDate->format('Y-m-d'),
                        'END_DATE' => $endDate ? $endDate->format('Y-m-d') : null,
                        'updated_at' => $now,
                    ]);
            }
        }

        $normalize = fn ($value) => strtolower(str_replace([' ', '-'], '_', (string) $value));

        $categoryMap = DB::table('event_categories')
            ->get()
            ->mapWithKeys(fn ($row) => [$normalize($row->EVENT_CATEGORY_VALUE) => $row->EVENT_CATEGORY_ID])
            ->toArray();

        $typeMap = DB::table('event_types')
            ->get()
            ->mapWithKeys(fn ($row) => [$normalize($row->EVENT_TYPE_VALUE) => $row->EVENT_TYPE_ID])
            ->toArray();

        if (isset($typeMap['online'])) {
            $typeMap['virtual'] = $typeMap['online'];
        }

        $events = [
            // All dates are intentionally set in the future relative to "today"
            // so the home page always shows upcoming events when seeds are run.
            [
                'EVENT_NAME' => 'Art Class by Bakery Prasad',
                'EVENT_LINK' => 'https://example.com/art-class',
                'COVER_PIC' => 'https://picsum.photos/seed/art-class/800/400',
                'TAGLINE' => 'Sketching roots, shaping futures',
                'DESCRIPTION' => 'Hands-on art class with community artists.',
                'EVENT_CATEGORY_ID' => $categoryMap['class'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addDays(1)->format('Y-m-d'),
                'START_TIME' => '10:00:00',
                'START_AM_PM' => 'AM',
                'END_DATE' => $today->copy()->addDays(1)->format('Y-m-d'),
                'END_TIME' => '12:00:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['hybrid'] ?? null,
                'LOCATION_NAME' => 'Online + Community Studio',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => 'MG Road',
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => 'Near Metro Station',
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                'RSVP_COUNT' => 10,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Book Reading by ARC',
                'EVENT_LINK' => 'https://example.com/book-reading',
                'COVER_PIC' => 'https://picsum.photos/seed/book-reading/800/400',
                'TAGLINE' => 'Stories of resilience',
                'DESCRIPTION' => 'Outdoor reading session in the park.',
                'EVENT_CATEGORY_ID' => $categoryMap['other'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addDays(2)->format('Y-m-d'),
                'START_TIME' => '14:00:00',
                'START_AM_PM' => 'PM',
                'END_DATE' => $today->copy()->addDays(2)->format('Y-m-d'),
                'END_TIME' => '16:00:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['in_person'] ?? null,
                'LOCATION_NAME' => 'Cubbon Park',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => 'Cubbon Park',
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => 'Band Stand',
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'MANDATORY',
                'RSVP_COUNT' => 20,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Constitution Study Circle',
                'EVENT_LINK' => 'https://example.com/study-circle',
                'COVER_PIC' => 'https://picsum.photos/seed/study-circle/800/400',
                'TAGLINE' => 'Learning together',
                'DESCRIPTION' => 'Group study and discussion on key constitutional topics.',
                'EVENT_CATEGORY_ID' => $categoryMap['panel'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addDays(3)->format('Y-m-d'),
                'START_TIME' => '18:30:00',
                'START_AM_PM' => 'PM',
                'END_DATE' => $today->copy()->addDays(3)->format('Y-m-d'),
                'END_TIME' => '20:00:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['in_person'] ?? null,
                'LOCATION_NAME' => 'Janata Library',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => 'JP Nagar',
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => 'Library Circle',
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                'RSVP_COUNT' => 31,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Ambedkarite Poetry Night',
                'EVENT_LINK' => 'https://example.com/poetry-night',
                'COVER_PIC' => 'https://picsum.photos/seed/poetry-night/800/400',
                'TAGLINE' => 'Words of freedom',
                'DESCRIPTION' => 'Online poetry sharing session.',
                'EVENT_CATEGORY_ID' => $categoryMap['celebration'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addDays(4)->format('Y-m-d'),
                'START_TIME' => '20:00:00',
                'START_AM_PM' => 'PM',
                'END_DATE' => $today->copy()->addDays(4)->format('Y-m-d'),
                'END_TIME' => '21:30:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['virtual'] ?? $typeMap['online'] ?? null,
                'LOCATION_NAME' => 'Online',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => null,
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => null,
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'HIDDEN',
                'RSVP_COUNT' => 54,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Online Workshop: Digital Safety',
                'EVENT_LINK' => 'https://example.com/digital-safety',
                'COVER_PIC' => 'https://picsum.photos/seed/digital-safety/800/400',
                'TAGLINE' => 'Stay safe online',
                'DESCRIPTION' => 'Practical tips for digital privacy and safety.',
                'EVENT_CATEGORY_ID' => $categoryMap['class'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addMonth()->format('Y-m-d'),
                'START_TIME' => '18:00:00',
                'START_AM_PM' => 'PM',
                'END_DATE' => $today->copy()->addMonth()->format('Y-m-d'),
                'END_TIME' => '19:30:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['virtual'] ?? $typeMap['online'] ?? null,
                'LOCATION_NAME' => 'Online',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => null,
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => null,
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                'RSVP_COUNT' => 61,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Bahujan Film Screening',
                'EVENT_LINK' => 'https://example.com/film-screening',
                'COVER_PIC' => 'https://picsum.photos/seed/film-screening/800/400',
                'TAGLINE' => 'Cinema for the people',
                'DESCRIPTION' => 'Screening and discussion of a Bahujan-focused film.',
                'EVENT_CATEGORY_ID' => $categoryMap['talk_series'] ?? $categoryMap['other'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addMonths(2)->format('Y-m-d'),
                'START_TIME' => '17:00:00',
                'START_AM_PM' => 'PM',
                'END_DATE' => $today->copy()->addMonths(2)->format('Y-m-d'),
                'END_TIME' => '19:30:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['in_person'] ?? null,
                'LOCATION_NAME' => 'Community Hall',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => 'Sheshadripuram',
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => 'Town Hall',
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                'RSVP_COUNT' => 0,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'EVENT_NAME' => 'Community Wellness Retreat',
                'EVENT_LINK' => 'https://example.com/wellness-retreat',
                'COVER_PIC' => 'https://picsum.photos/seed/wellness-retreat/800/400',
                'TAGLINE' => 'Rest, heal, organize',
                'DESCRIPTION' => 'Weekend retreat focused on wellness and collective care.',
                'EVENT_CATEGORY_ID' => $categoryMap['wellness_session'] ?? $categoryMap['other'] ?? null,
                'CURRENCY' => 'INR',
                'COST_DONATION' => '₹0',
                'START_DATE' => $today->copy()->addMonths(3)->format('Y-m-d'),
                'START_TIME' => '09:00:00',
                'START_AM_PM' => 'AM',
                'END_DATE' => $today->copy()->addMonths(3)->addDay()->format('Y-m-d'),
                'END_TIME' => '19:30:00',
                'END_AM_PM' => 'PM',
                'TIME_ZONE' => 'Asia/Kolkata',
                'EVENT_TYPE_ID' => $typeMap['hybrid'] ?? null,
                'LOCATION_NAME' => 'Retreat Centre + Online Check-ins',
                'COUNTRY' => 'India',
                'STATE_PROVINCE' => 'Karnataka',
                'CITY_TOWN' => 'Bangalore',
                'ADDRESS_LINE_1' => 'Kanakapura Road',
                'ADDRESS_LINE_2' => null,
                'LANDMARK' => 'Near Nature Park',
                'EVENT_VISIBILITY' => 'Public',
                'EVENT_RSVP' => 'Open',
                'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                'RSVP_COUNT' => 0,
                'APPROVAL_STATUS' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('events')->upsert(
            $events,
            ['EVENT_NAME', 'START_DATE', 'START_TIME'],
            [
                'EVENT_LINK',
                'COVER_PIC',
                'TAGLINE',
                'DESCRIPTION',
                'EVENT_CATEGORY_ID',
                'CURRENCY',
                'COST_DONATION',
                'START_AM_PM',
                'END_DATE',
                'END_TIME',
                'END_AM_PM',
                'TIME_ZONE',
                'EVENT_TYPE_ID',
                'LOCATION_NAME',
                'COUNTRY',
                'STATE_PROVINCE',
                'CITY_TOWN',
                'ADDRESS_LINE_1',
                'ADDRESS_LINE_2',
                'LANDMARK',
                'EVENT_VISIBILITY',
                'EVENT_RSVP',
                'RSVP_VISIBILITY',
                'RSVP_COUNT',
                'APPROVAL_STATUS',
                'updated_at',
            ]
        );
    }
}

