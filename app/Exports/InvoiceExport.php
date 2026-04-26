<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private ?string $status = null,
        private bool $onlyTrashed = false
    ) {}

    public function collection(): Collection
    {
        $query = Invoice::query()
            ->with(['organization:id,name', 'creator:id,name'])
            ->latest('id');

        if ($this->onlyTrashed) {
            $query->onlyTrashed();
        }

        if ($this->status !== null) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Invoice Number',
            'Invoice Date',
            'Due Date',
            'Organization',
            'Product',
            'Status',
            'Amount Collection',
            'Total',
            'Paid Amount',
            'Payment Date',
            'Created By',
            'Created At',
            'Archived At',
        ];
    }

    /**
     * @return list<int|float|string|null>
     */
    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->invoice_number,
            $invoice->invoice_date?->format('Y-m-d'),
            $invoice->due_date?->format('Y-m-d'),
            $invoice->organization?->name,
            $invoice->product,
            $invoice->status,
            (float) $invoice->amount_collection,
            (float) $invoice->total,
            (float) $invoice->paid_amount,
            $invoice->payment_date?->format('Y-m-d'),
            $invoice->creator?->name,
            $invoice->created_at?->format('Y-m-d H:i:s'),
            $invoice->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
