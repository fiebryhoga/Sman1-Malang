<?php

use App\Http\Controllers\Api\PresensiApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FonteWebhookController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Tambahkan baris ini
Route::get('/presensi/{nis}', [PresensiApiController::class, 'cekPresensi']);
// Route::post('/webhook/fonte', [FonteWebhookController::class, 'handle']);

Route::match(['get', 'post'], '/webhook/fonte', [FonteWebhookController::class, 'handle']);
