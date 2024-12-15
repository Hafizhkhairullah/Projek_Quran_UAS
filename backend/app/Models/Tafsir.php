<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tafsir extends Model
{
    use HasFactory;

    protected $table = 'tafsirs';

    protected $fillable = ['surat_nomor', 'nomor_ayat', 'teks_tafsir'];
}
