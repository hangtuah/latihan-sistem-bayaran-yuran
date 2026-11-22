<?php

use App\Http\Controllers\Bursary;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Route};

Route::get('/', function () {
    return view('welcome');
});

// Log masuk portal Bendahari (Sanctum SPA, cookie + CSRF)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);
    if (! Auth::attempt($credentials)) {
        return response()->json(['message' => 'Emel atau kata laluan salah.'], 422);
    }
    $request->session()->regenerate();

    return response()->json(['message' => 'ok']);
});

Route::middleware('auth')->group(function () {
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
});

// Pejabat Bendahari. Kawalan akses ditambah dalam Modul 3.
Route::prefix('bursary')->group(function () {
    Route::get('/payments', [Bursary\PaymentController::class, 'index']);
    Route::get('/overdue', [Bursary\OverdueInvoiceController::class, 'index']);
});
