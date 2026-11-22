Terima kasih. Bayaran anda telah diterima.

Invois:   {{ $payment->invoice->number }}
Amaun:    RM {{ number_format($payment->amount, 2) }}
Rujukan:  {{ $payment->reference }}
Tarikh:   {{ $payment->paid_at->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') }}

Pejabat Bendahari
