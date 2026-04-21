<?php

namespace App\Repositories\Invoices\Eloquent;

use App\Models\Invoice;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;

class InvoiceStatusHistoryRepository implements InvoiceStatusHistoryRepositoryInterface
{
    public function createInitialUnpaid(Invoice $invoice, string $note, ?int $changedByUserId): void
    {
        $invoice->statusHistories()->create([
            'from_status' => null,
            'from_status_value' => null,
            'to_status' => Invoice::STATUS_UNPAID,
            'to_status_value' => Invoice::STATUS_VALUE_UNPAID,
            'note' => $note,
            'changed_by_user_id' => $changedByUserId,
        ]);
    }

    public function createStatusChange(
        Invoice $invoice,
        ?string $fromStatus,
        ?int $fromStatusValue,
        string $toStatus,
        int $toStatusValue,
        ?float $paymentAmount,
        ?string $paymentDate,
        ?string $note,
        ?int $changedByUserId
    ): void {
        $invoice->statusHistories()->create([
            'from_status' => $fromStatus,
            'from_status_value' => $fromStatusValue,
            'to_status' => $toStatus,
            'to_status_value' => $toStatusValue,
            'payment_amount' => $paymentAmount,
            'payment_date' => $paymentDate,
            'note' => $note,
            'changed_by_user_id' => $changedByUserId,
        ]);
    }
}
