<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwalsholat extends Model
{
    use HasFactory;

    protected $table = 'jadwalsholats';

    protected $fillable = [
        'latitude',
        'longitude',
        'gregorian_date',
        'gregorian_weekday_en',
        'gregorian_day',
        'gregorian_month',
        'gregorian_year',
        'hijri_date',
        'hijri_weekday_en',
        'hijri_day',
        'hijri_month',
        'hijri_year',
        'imsak',
        'fajr',
        'sunrise',
        'dhuhr',
        'asr',
        'maghrib',
        'isha',
    ];

}
