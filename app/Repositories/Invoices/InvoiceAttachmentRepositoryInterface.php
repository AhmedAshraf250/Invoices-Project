<?php

namespace App\Repositories\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;

interface InvoiceAttachmentRepositoryInterface
{
    public function createForInvoice(
        Invoice $invoice,
        string $originalName,
        string $fileName,
        string $filePath,
        string $disk,
        string $mimeType,
        int $fileSize,
        ?int $uploadedByUserId
    ): InvoiceAttachment;

    public function delete(InvoiceAttachment $attachment): void;
}
