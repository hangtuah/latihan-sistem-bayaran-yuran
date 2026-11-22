<?php

namespace App\Http\Controllers\Bursary;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\InvoiceRepository;
use Illuminate\View\View;

final class OverdueInvoiceController extends Controller
{
    public function __construct(
        private InvoiceRepository $invoices,
    ) {}

    public function index(): View
    {
        return view('bursary.overdue', [
            'summary'  => $this->invoices->overdueSummary(),
            'invoices' => $this->invoices->overdue(),
        ]);
    }
}
