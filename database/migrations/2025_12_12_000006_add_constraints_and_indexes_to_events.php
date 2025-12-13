<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing string codes to lookup IDs before type change.
        $normalize = fn ($value) => strtolower(str_replace([' ', '-'], '_', (string) $value));

        $categoryMap = DB::table('event_categories')
            ->get()
            ->mapWithKeys(fn ($row) => [$normalize($row->EVENT_CATEGORY_VALUE) => $row->EVENT_CATEGORY_ID])
            ->toArray();

        $typeMap = DB::table('event_types')
            ->get()
            ->mapWithKeys(fn ($row) => [$normalize($row->EVENT_TYPE_VALUE) => $row->EVENT_TYPE_ID])
            ->toArray();

        // Alias "virtual" to "online" if present.
        if (isset($typeMap['online'])) {
            $typeMap['virtual'] = $typeMap['online'];
        }

        $events = DB::table('events')
            ->select('EVENT_ID', 'EVENT_CATEGORY_ID', 'EVENT_TYPE_ID')
            ->get();

        foreach ($events as $event) {
            $catId = $categoryMap[$normalize($event->EVENT_CATEGORY_ID)] ?? null;
            $typeId = $typeMap[$normalize($event->EVENT_TYPE_ID)] ?? null;

            DB::table('events')
                ->where('EVENT_ID', $event->EVENT_ID)
                ->update([
                    'EVENT_CATEGORY_ID' => $catId,
                    'EVENT_TYPE_ID' => $typeId,
                ]);
        }

        // Cast columns to bigint explicitly for Postgres before schema changes.
        DB::statement('ALTER TABLE events ALTER COLUMN "EVENT_CATEGORY_ID" TYPE BIGINT USING "EVENT_CATEGORY_ID"::bigint');
        DB::statement('ALTER TABLE events ALTER COLUMN "EVENT_TYPE_ID" TYPE BIGINT USING "EVENT_TYPE_ID"::bigint');

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('EVENT_CATEGORY_ID')->nullable()->change();
            $table->unsignedBigInteger('EVENT_TYPE_ID')->nullable()->change();

            // Indexes for common filters
            $table->index('EVENT_CATEGORY_ID', 'events_category_idx');
            $table->index('EVENT_TYPE_ID', 'events_type_idx');
            $table->index('START_DATE', 'events_start_date_idx');
            $table->index(['START_DATE', 'START_TIME'], 'events_start_date_time_idx');

            // Foreign keys to lookup tables
            $table->foreign('EVENT_CATEGORY_ID', 'events_category_fk')
                ->references('EVENT_CATEGORY_ID')
                ->on('event_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('EVENT_TYPE_ID', 'events_type_fk')
                ->references('EVENT_TYPE_ID')
                ->on('event_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('events_category_fk');
            $table->dropForeign('events_type_fk');

            $table->dropIndex('events_category_idx');
            $table->dropIndex('events_type_idx');
            $table->dropIndex('events_start_date_idx');
            $table->dropIndex('events_start_date_time_idx');
        });
    }
};

