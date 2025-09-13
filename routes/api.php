<?php

use App\Http\Controllers\Api\PresensiApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BaileysWebhookController; 

use App\Http\Controllers\Api\ApiPresensiController;
use App\Http\Controllers\Api\PelanggaranNotificationController;





use App\Http\Controllers\Api\DisciplinaryPointController;


Route::get('/presensi/{nis}', [PresensiApiController::class, 'cekPresensi']);

Route::post('/webhook/baileys', [BaileysWebhookController::class, 'handle']);



Route::post('/disciplinary-notification', [DisciplinaryPointController::class, 'sendNotification']);



Route::post('/pelanggaran/{record}/send-notification', [PelanggaranNotificationController::class, 'sendNotification']);



// Route::match(['get', 'post'], '/webhook/fonte', [FonteWebhookController::class, 'handle']);



Route::post('/presensi/check-in', [ApiPresensiController::class, 'checkIn']);