<?php

namespace App\Repositories\Invoices\Eloquent;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Repositories\Invoices\InvoiceAttachmentRepositoryInterface;

class InvoiceAttachmentRepository implements InvoiceAttachmentRepositoryInterface
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
    ): InvoiceAttachment {
        return $invoice->attachments()->create([
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'uploaded_by_user_id' => $uploadedByUserId,
        ]);
    }

    public function delete(InvoiceAttachment $attachment): void
    {
        $attachment->delete();
    }
}
