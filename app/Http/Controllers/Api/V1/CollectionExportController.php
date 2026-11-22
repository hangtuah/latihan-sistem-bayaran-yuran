<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ExportCollectionReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CollectionExportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        ExportCollectionReport::dispatch($request->user(), $request->integer('year'))
            ->onQueue('reports');

        return response()->json([
            'message' => 'Laporan sedang dijana. Ia akan dihantar ke emel anda.',
        ], 202);
    }
}
