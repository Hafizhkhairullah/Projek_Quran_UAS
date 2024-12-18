<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlquranController;

Route::get('/surah', [AlquranController::class, 'surah']);
Route::get('/surah/import-ayat', [AlquranController::class, 'importAyat']);
Route::get('/surah/{nomor}', [AlquranController::class, 'Ayat']);
Route::get('/tafsir/import-tafsir', [AlquranController::class, 'importTafsir']);
Route::get('/tafsir/{nomor}', [AlquranController::class, 'tafsir']);
Route::get('/import-jadwalsholat', [AlquranController::class, 'importJadwalsholat']);
Route::get('/jadwalsholat', [AlquranController::class, 'jadwalsholat']);
Route::get('/jadwalsholat/{year}', [AlquranController::class, 'jadwalsholatYM']);
Route::get('/jadwalsholat/{year}/{month}', [AlquranController::class, 'jadwalsholatM']);

