<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwalsholat extends Model
{
    use HasFactory;

    protected $table = 'jadwalsholats';

    protected $fillable = ['date', 'fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

}
