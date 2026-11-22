@extends('bursary.layout')

@section('title', 'Bayaran terkini')

@section('content')
<h1>50 bayaran terkini</h1>
<table>
    <thead>
        <tr>
            <th>Rujukan</th><th>Kaedah</th><th>Jumlah (RM)</th>
            <th>Matrik</th><th>Nama</th><th>Program</th>
            <th>Invois</th><th>Jenis yuran</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($payments as $payment)
        <tr>
            <td>{{ $payment->reference }}</td>
            <td>{{ $payment->method->value }}</td>
            <td class="num">{{ number_format($payment->amount, 2) }}</td>
            <td>{{ $payment->student->matric }}</td>
            <td>{{ $payment->student->name }}</td>
            <td>{{ $payment->student->programme->code }}</td>
            <td>{{ $payment->invoice->number }}</td>
            <td>{{ $payment->invoice->type->value }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
