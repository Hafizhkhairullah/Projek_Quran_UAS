<?php

use App\Http\Controllers\AyatController;
use App\Http\Controllers\SurahController;
use App\Http\Controllers\TafsirController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/surah', [SurahController::class, 'surah']);
Route::get('/surah/all', [AyatController::class, 'index']);
Route::get('/surah/{nomor}', [AyatController::class, 'ayat']);
Route::get('/tafsir/all', [TafsirController::class, 'index']);
Route::get('/tafsir/{nomor}', [TafsirController::class, 'tafsir']);