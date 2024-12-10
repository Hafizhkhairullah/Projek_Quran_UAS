<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';
    protected $fillable = ['nomor', 'nama', 'nama_latin', 'arti', 'jumlah_ayat', 'tempat_turun', 'audio_full'];

    protected $casts = ['audio_full' => 'array'];
}
