<?php

use App\Http\Controllers\SuratController;
use App\Http\Controllers\AyatController;
use App\Http\Controllers\TafsirController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/surat', [SuratController::class, 'index']);
Route::get('/surat/all', [AyatController::class, 'index']);
Route::get('/surat/{nomor}', [AyatController::class, 'ayat']);
Route::get('/tafsir/all', [TafsirController::class, 'index']);
Route::get('/tafsir/{nomor}', [TafsirController::class, 'tafsir']);