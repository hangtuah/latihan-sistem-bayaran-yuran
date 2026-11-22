<?php

namespace App\Http\Controllers;

use App\Clients\CardClient;
use App\Clients\EWalletClient;
use App\Clients\FpxClient;
use App\Mail\PaymentReceipt;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

// Kod asal sistem yuran: satu method buat semua benda.
// Modul 1 memecahkan controller ini kepada service, gateway dan repository.
class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'method'     => 'required|in:fpx,card,ewallet',
        ]);
        $invoice = Invoice::find($request->invoice_id);
        if ($invoice->paid_at) {
            return back()->withErrors(['invoice_id' => 'Invois sudah dibayar.']);
        }
        $amount = $invoice->amount;
        if (now()->gt($invoice->due_date)) {
            if ($invoice->type == 'semester') { $amount = $amount * 1.05; }
            elseif ($invoice->type == 'hostel') { $amount = $amount * 1.02; }
        }
        if ($request->input('method') == 'fpx') {
            $ref = (new FpxClient(env('FPX_KEY')))->pay($invoice->number, $amount);
        } elseif ($request->input('method') == 'card') {
            $ref = (new CardClient(env('CARD_KEY')))->charge($amount);
        } else { $ref = (new EWalletClient(env('EWALLET_KEY')))->debit($amount); }

        $payment = Payment::create($request->all()
            + ['student_id' => auth()->user()->student->id, 'amount' => $amount, 'reference' => $ref, 'paid_at' => now()]);
        $invoice->update(['paid_at' => now()]);
        Mail::to(auth()->user())->send(new PaymentReceipt($payment));
        return redirect()->route('payments.show', $payment);
    }

    public function show(Payment $payment)
    {
        return view('payments.show', ['payment' => $payment]);
    }
}
