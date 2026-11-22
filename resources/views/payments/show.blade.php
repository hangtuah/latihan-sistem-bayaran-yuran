<!doctype html>
<html lang="ms">
<head><meta charset="utf-8"><title>Resit {{ $payment->reference }}</title></head>
<body>
    <h1>Resit bayaran</h1>
    @if (session('status'))<p>{{ session('status') }}</p>@endif
    <dl>
        <dt>Invois</dt><dd>{{ $payment->invoice->number }}</dd>
        <dt>Amaun</dt><dd>RM {{ number_format($payment->amount, 2) }}</dd>
        <dt>Rujukan</dt><dd>{{ $payment->reference }}</dd>
    </dl>
</body>
</html>
