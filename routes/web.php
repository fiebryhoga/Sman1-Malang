<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\HomeController; // <-- 1. Tambahkan import ini
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 2. Ganti rute ini untuk menunjuk ke HomeController
Route::get('/', [HomeController::class, 'index']);

// Rute-rute lain tetap sama
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route::get('/excel/export', [ExcelExportController::class, 'export'])->name('excel.export');


Route::get('/rekap-presensi/export', [ExcelExportController::class, 'exportRekapPresensi'])
    ->name('presensi.export_rekap')
    ->middleware('auth');



Route::get('/jurnal-guru/export', [ExcelExportController::class, 'exportJurnalGuru'])
    ->name('presensi.export_jurnal')
    ->middleware('auth');



    