<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tafsir extends Model
{
    use HasFactory;

    protected $table = 'tafsirs';

    protected $fillable = ['nomor_surat', 'nomor_ayat', 'teks_tafsir'];
}
