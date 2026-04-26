<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateInvoiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Invoice::STATUS_PAID, Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $invoice = $this->route('invoice');

                if (! $invoice instanceof Invoice) {
                    return;
                }

                $status = $this->string('status')->toString();
                $paymentAmount = (float) $this->input('payment_amount', 0);
                $total = (float) $invoice->total;
                $currentPaidAmount = (float) $invoice->paid_amount;

                if ($invoice->status === Invoice::STATUS_PAID) {
                    $validator->errors()->add('status', __('invoices.validation.paid_status_locked'));

                    return;
                }

                if ($status === $invoice->status && $status !== Invoice::STATUS_PARTIAL) {
                    $validator->errors()->add('status', __('invoices.validation.same_status_not_allowed'));
                }

                if ($status === Invoice::STATUS_PAID && $this->filled('payment_amount') && $paymentAmount < $total) {
                    $validator->errors()->add('payment_amount', __('invoices.validation.payment_amount_paid'));
                }

                if ($status === Invoice::STATUS_PARTIAL && ($paymentAmount <= 0 || $paymentAmount >= $total)) {
                    $validator->errors()->add('payment_amount', __('invoices.validation.payment_amount_partial'));
                }

                if (
                    $status === Invoice::STATUS_PARTIAL
                    && $invoice->status === Invoice::STATUS_PARTIAL
                    && ($currentPaidAmount + $paymentAmount) > $total
                ) {
                    $validator->errors()->add('payment_amount', __('invoices.validation.payment_amount_partial_exceeds_total'));
                }
            },
        ];
    }
}
