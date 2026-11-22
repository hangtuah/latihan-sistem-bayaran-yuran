<?php

use App\Http\Controllers\Bursary;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
});

// Pejabat Bendahari. Kawalan akses ditambah dalam Modul 3.
Route::prefix('bursary')->group(function () {
    Route::get('/payments', [Bursary\PaymentController::class, 'index']);
});
