<?php

// Mengimpor kelas-kelas yang diperlukan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlquranController;
use App\Http\Middleware\CorsMiddleware;

// Mendefinisikan rute API yang menggunakan middleware CORS
Route::middleware([CorsMiddleware::class])->group(function () { 
    
    // Rute untuk mendapatkan data surah
    Route::get('/surah', [AlquranController::class, 'surah']);

    // Rute untuk mengimpor data ayat
    Route::get('/surah/import-ayat', [AlquranController::class, 'importAyat']);

    // Rute untuk mendapatkan data ayat berdasarkan nomor surah
    Route::get('/surah/{nomor}', [AlquranController::class, 'Ayat']);

    // Rute untuk mengimpor data tafsir
    Route::get('/tafsir/import-tafsir', [AlquranController::class, 'importTafsir']);

    // Rute untuk mendapatkan data tafsir berdasarkan nomor surah
    Route::get('/tafsir/{nomor}', [AlquranController::class, 'tafsir']);

    // Rute untuk mendapatkan data tafsir berdasarkan nomor surah dan nomor ayat
    Route::get('/tafsir/{nomorSurah}/{nomorAyat}', [AlquranController::class, 'tafsirAyat']);
});
