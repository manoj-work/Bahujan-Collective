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

        $demoEvents = [
            ['Art Class by Bakery Prasad', 'Sketching roots, shaping futures', 'Hands-on art class with community artists.', 'class', 'hybrid', '2026-06-10', '09:30:00', '11:30:00', 'Delhi Ambedkar Study Hall', 'Delhi', 'Delhi', '26 Alipur Road', 'Civil Lines', 'Near Vidhan Sabha Metro', 18],
            ['Book Reading by ARC', 'Stories of resilience', 'Community reading and discussion circle.', 'reading_session', 'in_person', '2026-06-10', '12:00:00', '13:30:00', 'Jantar Mantar Community Tent', 'Delhi', 'Delhi', 'Jantar Mantar Road', null, 'Near Parliament Street', 24],
            ['Constitution Study Circle', 'Learning together', 'Group study on constitutional values and public life.', 'panel', 'in_person', '2026-06-10', '15:00:00', '17:00:00', 'Saket Community Library', 'Delhi', 'Delhi', 'Press Enclave Road', 'Saket', 'Near Select Citywalk', 31],
            ['Ambedkarite Poetry Night', 'Words of freedom', 'Poetry sharing and performances from community artists.', 'celebration', 'in_person', '2026-06-10', '18:00:00', '20:00:00', 'Dadar Social Justice Centre', 'Mumbai', 'Maharashtra', 'Dadar West', null, 'Near Chaityabhoomi', 45],
            ['Online Workshop: Digital Safety', 'Stay safe online', 'Practical digital privacy workshop for organizers.', 'class', 'hybrid', '2026-06-10', '20:30:00', '22:00:00', 'Mumbai Digital Rights Hub', 'Mumbai', 'Maharashtra', 'Bandra Kurla Complex', 'Bandra East', 'Near MMRDA Grounds', 52],
            ['Bahujan Film Screening', 'Cinema for the people', 'Screening and discussion of a Bahujan-focused documentary.', 'talk_series', 'in_person', '2026-06-15', '10:00:00', '12:30:00', 'Pune Peoples Theatre', 'Pune', 'Maharashtra', 'FC Road', null, 'Near Deccan Gymkhana', 40],
            ['Community Wellness Retreat', 'Rest, heal, organize', 'A day-long community wellness and collective care gathering.', 'wellness_session', 'hybrid', '2026-06-15', '14:00:00', '17:00:00', 'Yerwada Community Centre', 'Pune', 'Maharashtra', 'Nagar Road', 'Yerwada', 'Near Yerwada Post Office', 28],
            ['Anti-Caste Reading Circle', 'Read, reflect, organize', 'A guided reading session on anti-caste literature.', 'reading_session', 'in_person', '2026-06-15', '18:30:00', '20:00:00', 'Kothrud Study Space', 'Pune', 'Maharashtra', 'Karve Road', 'Kothrud', 'Near Paud Phata', 22],
            ['Bahujan Art Museum Walk', 'Art, memory, resistance', 'A guided museum walk followed by community conversation.', 'art_museum_display', 'in_person', '2026-06-20', '11:00:00', '13:00:00', 'Albert Hall Museum Steps', 'Jaipur', 'Rajasthan', 'Museum Road', 'Ram Niwas Garden', 'Near Albert Hall Museum', 35],
            ['Dalit History Talk Series', 'Histories from below', 'Public talk series on Dalit histories and community archives.', 'talk_series', 'hybrid', '2026-06-20', '16:00:00', '18:00:00', 'Jaipur Social Justice Forum', 'Jaipur', 'Rajasthan', 'MI Road', null, 'Near Ajmeri Gate', 26],
        ];

        foreach ($demoEvents as $event) {
            [$name, $tagline, $description, $category, $type, $date, $startTime, $endTime, $locationName, $city, $state, $address1, $address2, $landmark, $rsvpCount] = $event;

            DB::table('events')->updateOrInsert(
                ['EVENT_NAME' => $name],
                [
                    'EVENT_LINK' => 'https://example.com/' . str($name)->slug(),
                    'COVER_PIC' => 'https://picsum.photos/seed/' . str($name)->slug() . '/800/400',
                    'TAGLINE' => $tagline,
                    'DESCRIPTION' => $description,
                    'EVENT_CATEGORY_ID' => $categoryMap[$category] ?? $categoryMap['other'] ?? null,
                    'CURRENCY' => 'INR',
                    'COST_DONATION' => '0',
                    'START_DATE' => $date,
                    'START_TIME' => $startTime,
                    'START_AM_PM' => Carbon::parse($startTime)->format('A'),
                    'END_DATE' => $date,
                    'END_TIME' => $endTime,
                    'END_AM_PM' => Carbon::parse($endTime)->format('A'),
                    'TIME_ZONE' => 'Asia/Kolkata',
                    'EVENT_TYPE_ID' => $typeMap[$type] ?? $typeMap['online'] ?? null,
                    'LOCATION_NAME' => $locationName,
                    'COUNTRY' => 'India',
                    'STATE_PROVINCE' => $state,
                    'CITY_TOWN' => $city,
                    'ADDRESS_LINE_1' => $address1,
                    'ADDRESS_LINE_2' => $address2,
                    'LANDMARK' => $landmark,
                    'EVENT_VISIBILITY' => 'Public',
                    'EVENT_RSVP' => 'Open',
                    'RSVP_VISIBILITY' => 'ATTENDEE_CHOICE',
                    'RSVP_COUNT' => $rsvpCount,
                    'APPROVAL_STATUS' => 'approved',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
