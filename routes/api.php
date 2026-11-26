<?php

use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\TokenController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('v1')->group(function () {
    Route::post('tokens', [TokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])
            ->middleware(CheckAbilities::class.':invoices:read');

        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
            ->middleware(CheckAbilities::class.':invoices:read');
    });
});
