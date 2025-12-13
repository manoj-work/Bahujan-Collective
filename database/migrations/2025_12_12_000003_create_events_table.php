<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('EVENT_ID');
            $table->string('EVENT_NAME', 100);
            $table->string('EVENT_LINK', 100)->nullable();
            $table->string('COVER_PIC', 2048)->nullable(); // store path/URL to the image
            $table->string('TAGLINE', 100)->nullable();
            $table->text('DESCRIPTION')->nullable(); // up to ~64KB, covers 1000 chars
            $table->string('EVENT_CATEGORY_ID', 100)->nullable();
            $table->string('CURRENCY', 10)->nullable();
            $table->string('COST_DONATION', 50)->nullable();
            $table->date('START_DATE')->nullable();
            $table->time('START_TIME')->nullable();
            $table->string('START_AM_PM', 2)->nullable();
            $table->date('END_DATE')->nullable();
            $table->time('END_TIME')->nullable();
            $table->string('END_AM_PM', 2)->nullable();
            $table->string('TIME_ZONE', 100)->nullable();
            $table->string('EVENT_TYPE_ID', 100)->nullable(); // in-person, online, hybrid
            $table->string('LOCATION_NAME')->nullable();
            $table->string('COUNTRY', 100)->nullable();
            $table->string('STATE_PROVINCE', 100)->nullable();
            $table->string('CITY_TOWN', 100)->nullable();
            $table->string('ADDRESS_LINE_1')->nullable();
            $table->string('ADDRESS_LINE_2')->nullable();
            $table->string('LANDMARK')->nullable();
            $table->string('EVENT_VISIBILITY', 10)->nullable(); // Restricted / Unrestricted
            $table->string('EVENT_RSVP', 10)->nullable(); // Restricted / Unrestricted
            $table->string('RSVP_VISIBILITY', 20)->nullable(); // MANDATORY, ATTENDEE_CHOICE, HIDDEN
            $table->unsignedInteger('RSVP_COUNT')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

