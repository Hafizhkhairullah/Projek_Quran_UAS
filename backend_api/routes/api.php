<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlquranController;

Route::get('/surah', [AlquranController::class, 'surah']);
Route::get('/surah/import-ayat', [AlquranController::class, 'importAyat']);
Route::get('/surah/{nomor}', [AlquranController::class, 'Ayat']);

