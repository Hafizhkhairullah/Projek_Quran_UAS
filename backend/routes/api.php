<?php

use App\Http\Controllers\SuratController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/surat', [SuratController::class, 'index']);