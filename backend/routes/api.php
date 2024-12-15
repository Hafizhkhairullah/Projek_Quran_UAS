<?php

use App\Http\Controllers\SurahController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/surah', [SurahController::class, 'index']);
