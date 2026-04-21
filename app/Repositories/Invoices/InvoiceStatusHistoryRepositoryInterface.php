<?php

namespace App\Repositories\Invoices;

use App\Models\Invoice;

interface InvoiceStatusHistoryRepositoryInterface
{
    public function createInitialUnpaid(Invoice $invoice, string $note, ?int $changedByUserId): void;

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
    ): void;
}
