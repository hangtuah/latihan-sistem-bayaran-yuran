<?php

namespace App\Http\Controllers\Bursary;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\View\View;

final class PaymentController extends Controller
{
    /** 50 bayaran terkini untuk Pejabat Bendahari. */
    public function index(): View
    {
        $payments = Payment::latest('paid_at')->limit(50)->get();

        return view('bursary.payments', ['payments' => $payments]);
    }
}
