<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{
    use HasFactory;

    protected $table = 'ayats';

    protected $fillable = ['surat_nomor', 'nomor_ayat', 'teks_arab', 'teks_latin', 'teks_terjemahan'];
}
