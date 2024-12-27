<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $table = 'surahs';

    protected $fillable = ['nomor', 'nama', 'nama_latin', 'arti', 'jumlah_ayat', 'tempat_turun', 'audio_full'];

    protected $cats = ['audio_full' => 'array'];
}
