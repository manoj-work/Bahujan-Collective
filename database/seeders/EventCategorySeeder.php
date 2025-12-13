<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventCategorySeeder extends Seeder
{
    /**
     * Seed the event_categories table.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            ['EVENT_CATEGORY_ID' => 1, 'EVENT_CATEGORY_VALUE' => 'Class', 'EVENT_CATEGORY_DESCRIPTION' => 'Class'],
            ['EVENT_CATEGORY_ID' => 2, 'EVENT_CATEGORY_VALUE' => 'Protest', 'EVENT_CATEGORY_DESCRIPTION' => 'Protest'],
            ['EVENT_CATEGORY_ID' => 3, 'EVENT_CATEGORY_VALUE' => 'Reading Session', 'EVENT_CATEGORY_DESCRIPTION' => 'Reading Session'],
            ['EVENT_CATEGORY_ID' => 4, 'EVENT_CATEGORY_VALUE' => 'Wellness Session', 'EVENT_CATEGORY_DESCRIPTION' => 'Wellness Session'],
            ['EVENT_CATEGORY_ID' => 5, 'EVENT_CATEGORY_VALUE' => 'Celebration', 'EVENT_CATEGORY_DESCRIPTION' => 'Celebration'],
            ['EVENT_CATEGORY_ID' => 6, 'EVENT_CATEGORY_VALUE' => 'Panel', 'EVENT_CATEGORY_DESCRIPTION' => 'Panel'],
            ['EVENT_CATEGORY_ID' => 7, 'EVENT_CATEGORY_VALUE' => 'Book Release', 'EVENT_CATEGORY_DESCRIPTION' => 'Book Release'],
            ['EVENT_CATEGORY_ID' => 8, 'EVENT_CATEGORY_VALUE' => 'Art Museum Display', 'EVENT_CATEGORY_DESCRIPTION' => 'Art Museum Display'],
            ['EVENT_CATEGORY_ID' => 9, 'EVENT_CATEGORY_VALUE' => 'Talk Series', 'EVENT_CATEGORY_DESCRIPTION' => 'Talk Series'],
            ['EVENT_CATEGORY_ID' => 10, 'EVENT_CATEGORY_VALUE' => 'Other', 'EVENT_CATEGORY_DESCRIPTION' => 'Other'],
        ];

        DB::table('event_categories')->upsert(
            array_map(
                fn ($cat) => $cat + ['created_at' => $now, 'updated_at' => $now],
                $categories
            ),
            ['EVENT_CATEGORY_ID'],
            ['EVENT_CATEGORY_VALUE', 'EVENT_CATEGORY_DESCRIPTION', 'updated_at']
        );
    }
}

