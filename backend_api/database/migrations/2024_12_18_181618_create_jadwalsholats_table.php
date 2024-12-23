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
        Schema::create('jadwalsholats', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 15, 8);
            $table->decimal('longitude', 15, 8);
            $table->string('gregorian_date');
            $table->string('gregorian_weekday_en');
            $table->string('gregorian_day');
            $table->string('gregorian_month');
            $table->string('gregorian_year');
            $table->string('hijri_date');
            $table->string('hijri_weekday_en');
            $table->string('hijri_day');
            $table->string('hijri_month');
            $table->string('hijri_year');
            $table->string('imsak');
            $table->string('fajr');
            $table->string('sunrise');
            $table->string('dhuhr');
            $table->string('asr');
            $table->string('maghrib');
            $table->string('isha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwalsholats');
    }
};
