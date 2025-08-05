<?php

use App\Http\Controllers\Api\PresensiApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BaileysWebhookController; // Tambahkan ini



use App\Http\Controllers\Api\DisciplinaryPointController;


Route::get('/presensi/{nis}', [PresensiApiController::class, 'cekPresensi']);

Route::post('/webhook/baileys', [BaileysWebhookController::class, 'handle']);



Route::post('/disciplinary-notification', [DisciplinaryPointController::class, 'sendNotification']);


// Nonaktifkan rute Fonte jika sudah tidak digunakan
// Route::match(['get', 'post'], '/webhook/fonte', [FonteWebhookController::class, 'handle']);