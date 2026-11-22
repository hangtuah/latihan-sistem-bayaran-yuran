<?php

use App\Http\Controllers\Api\V1\{CollectionExportController, InvoiceController, InvoiceSearchController, PartnerPaymentController,
    PaymentController, ProfileController, ReceiptController, TokenController};
use App\Http\Controllers\Api\V2\InvoiceController as V2InvoiceController;
use App\Http\Middleware\AssignRequestId;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
use Laravel\Sanctum\Http\Middleware\{CheckAbilities, EnsureFrontendRequestsAreStateful};

Route::prefix('v1')->middleware(AssignRequestId::class)->group(function () {
    Route::post('tokens', [TokenController::class, 'store'])->middleware('throttle:login');

    // Sistem kewangan universiti: Passport client credentials
    Route::get('partner/payments', [PartnerPaymentController::class, 'index'])
        ->middleware(EnsureClientIsResourceOwner::using('payments:read'));

    Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum', 'throttle:api'])
        ->group(function () {
            Route::delete('tokens/current', [TokenController::class, 'destroy']);
            Route::get('invoices', [InvoiceController::class, 'index'])
                ->middleware(CheckAbilities::class.':invoices:read');
            Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
                ->middleware(CheckAbilities::class.':invoices:read');
            Route::post('payments', [PaymentController::class, 'store'])
                ->middleware([CheckAbilities::class.':payments:create', 'throttle:payments']);
            Route::patch('profile', [ProfileController::class, 'update']);
            Route::get('receipts/{payment}', [ReceiptController::class, 'show']);
            Route::get('reports/payments-today', [ReceiptController::class, 'today'])
                ->middleware('can:view-bursary-reports');

            Route::middleware('can:view-bursary-reports')->group(function () {
                Route::get('reports/collections', function () {
                    $day = now('Asia/Kuala_Lumpur');

                    return [
                        'date' => $day->toDateString(),
                        'total' => Payment::whereBetween('paid_at', [
                            $day->copy()->startOfDay()->utc(),
                            $day->copy()->endOfDay()->utc(),
                        ])->sum('amount'),
                    ];
                });
                Route::get('search/invoices', InvoiceSearchController::class);
                Route::post('reports/collections/exports', [CollectionExportController::class, 'store']);
            });
        });
});

Route::prefix('v2')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('invoices/{invoice}', [V2InvoiceController::class, 'show']);
});
