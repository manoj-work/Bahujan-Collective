<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    /**
     * Seed the event_types table.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            ['EVENT_TYPE_ID' => 1, 'EVENT_TYPE_VALUE' => 'In-person', 'EVENT_TYPE_DESCRIPTION' => 'In-person'],
            ['EVENT_TYPE_ID' => 2, 'EVENT_TYPE_VALUE' => 'Online', 'EVENT_TYPE_DESCRIPTION' => 'Online'],
            ['EVENT_TYPE_ID' => 3, 'EVENT_TYPE_VALUE' => 'Hybrid', 'EVENT_TYPE_DESCRIPTION' => 'Hybrid'],
        ];

        DB::table('event_types')->upsert(
            array_map(
                fn ($type) => $type + ['created_at' => $now, 'updated_at' => $now],
                $types
            ),
            ['EVENT_TYPE_ID'],
            ['EVENT_TYPE_VALUE', 'EVENT_TYPE_DESCRIPTION', 'updated_at']
        );
    }
}

