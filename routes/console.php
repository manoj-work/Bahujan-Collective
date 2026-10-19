<?php

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:move-past-to-upcoming', function () {
    $today = Carbon::today();
    $updated = 0;

    $pastEvents = DB::table('events')
        ->select('EVENT_ID', 'START_DATE', 'END_DATE')
        ->whereNotNull('START_DATE')
        ->whereDate('START_DATE', '<', $today)
        ->get();

    foreach ($pastEvents as $event) {
        $startDate = Carbon::parse($event->START_DATE);
        $endDate = $event->END_DATE ? Carbon::parse($event->END_DATE) : null;

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
                'updated_at' => now(),
            ]);

        $updated++;
    }

    $this->info("Moved {$updated} past event(s) to current/future dates.");
})->purpose('Move events with past START_DATE to current or future dates so they show on the home page');
