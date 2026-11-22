<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class CollectionExportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $year = $request->integer('year');
        $path = "reports/kutipan-{$year}.csv";
        Storage::makeDirectory('reports');
        $handle = fopen(Storage::path($path), 'w');
        fputcsv($handle, ['rujukan', 'invois', 'kaedah', 'amaun', 'tarikh']);

        Payment::with('invoice')->whereYear('paid_at', $year)->lazyById(1000)
            ->each(fn (Payment $p) => fputcsv($handle, [
                $p->reference, $p->invoice->number, $p->method->value, $p->amount, $p->paid_at,
            ]));
        fclose($handle);

        return response()->json(['path' => $path], 201);
    }
}
