<?php

use App\Http\Controllers\SuratController;
use App\Http\Controllers\AyatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/surat', [SuratController::class, 'index']);
Route::get('/surat/all', [AyatController::class, 'index']);
Route::get('/surat/{nomor}', [AyatController::class, 'ayat']);