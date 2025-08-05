<?php

use App\Http\Controllers\Api\PresensiApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BaileysWebhookController; // Tambahkan ini

Route::get('/presensi/{nis}', [PresensiApiController::class, 'cekPresensi']);

Route::post('/webhook/baileys', [BaileysWebhookController::class, 'handle']);

// Nonaktifkan rute Fonte jika sudah tidak digunakan
// Route::match(['get', 'post'], '/webhook/fonte', [FonteWebhookController::class, 'handle']);