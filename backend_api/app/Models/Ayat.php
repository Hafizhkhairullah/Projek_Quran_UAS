<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Ayat extends Model
{
    use HasFactory;

    protected $table = 'ayats';

    protected $fillable = ['nomor_surah', 'nomor_ayat', 'teks_arab', 'teks_latin', 'teks_terjemahan', 'audio'];

    protected $casts = ['audio' => 'array'];
}
