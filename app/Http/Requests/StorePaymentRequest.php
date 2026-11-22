<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = Invoice::find($this->integer('invoice_id'));

        return $invoice !== null
            && $this->user()->can('pay', $invoice);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'method'     => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }
}
