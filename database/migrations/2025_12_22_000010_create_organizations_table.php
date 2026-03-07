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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('ORG_ID');
            $table->string('ORG_NAME', 100);

            $table->string('LEADERSHIP_COMPOSITION', 100)->nullable();
            $table->string('PRIMARY_CONTACT', 255)->nullable();
            $table->string('IS_PRIMARY_CONTACT_BAHUJAN', 10)->nullable();

            $table->string('WEBSITE', 100)->nullable();
            $table->string('INSTAGRAM', 100)->nullable();
            $table->string('EMAIL', 100)->nullable();

            $table->string('PHONE_COUNTRY_CODE', 10)->nullable();
            $table->string('PHONE_NUMBER', 30)->nullable();
            $table->string('WHATSAPP_COUNTRY_CODE', 10)->nullable();
            $table->string('WHATSAPP_NUMBER', 30)->nullable();

            $table->string('COVER_PIC', 2048)->nullable();
            $table->string('DESCRIPTION', 1000)->nullable();

            $table->string('ORG_TIME_ZONE', 100)->nullable();
            $table->string('LOCATION_NAME')->nullable();
            $table->string('COUNTRY', 100)->nullable();
            $table->string('STATE_PROVINCE', 100)->nullable();
            $table->string('CITY_TOWN', 100)->nullable();
            $table->string('ADDRESS_LINE_1')->nullable();
            $table->string('ADDRESS_LINE_2')->nullable();
            $table->string('LANDMARK')->nullable();

            $table->string('GEO_LOCATION', 10)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};





