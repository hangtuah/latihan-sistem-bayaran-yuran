@extends('bursary.layout')

@section('title', 'Invois lewat')

@section('content')
<h1>{{ number_format($summary['total_count']) }} invois lewat</h1>
<p>Jumlah tertunggak: RM {{ number_format($summary['total_amount'], 2) }}</p>

<table>
    <thead>
        <tr><th>Invois</th><th>Matrik</th><th>Nama</th><th>Program</th><th>Amaun (RM)</th><th>Tarikh akhir</th></tr>
    </thead>
    <tbody>
    @foreach ($invoices as $invoice)
        <tr>
            <td>{{ $invoice->number }}</td>
            <td>{{ $invoice->student->matric }}</td>
            <td>{!! $invoice->student->name !!}</td>
            <td>{{ $invoice->student->programme->code }}</td>
            <td class="num">{{ number_format($invoice->amount, 2) }}</td>
            <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
