<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlquranController;
use App\Http\Middleware\CorsMiddleware;

Route::middleware([CorsMiddleware::class])->group(function () { 
    Route::get('/surah', [AlquranController::class, 'surah']);
    Route::get('/surah/import-ayat', [AlquranController::class, 'importAyat']);
    Route::get('/surah/{nomor}', [AlquranController::class, 'Ayat']);
    Route::get('/tafsir/import-tafsir', [AlquranController::class, 'importTafsir']);
    Route::get('/tafsir/{nomor}', [AlquranController::class, 'tafsir']);
});
