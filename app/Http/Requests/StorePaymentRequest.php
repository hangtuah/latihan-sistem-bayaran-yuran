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
        // pelajar hanya boleh bayar invois sendiri
        return Invoice::whereKey($this->integer('invoice_id'))
            ->where('student_id', $this->user()->student->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'method'     => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }
}
