<?php

namespace App\Services\Invoices\DTOs;

use App\Models\Product;

class CreateInvoiceData
{
    public function __construct(
        public readonly string $invoiceNumber,
        public readonly ?string $externalInvoiceNumber,
        public readonly string $invoiceDate,
        public readonly ?string $dueDate,
        public readonly Product $product,
        public readonly int $organizationId,
        public readonly float $amountCollection,
        public readonly float $commissionRate,
        public readonly float $amountCommission,
        public readonly string $discountType,
        public readonly float $discountValue,
        public readonly float $discountAmount,
        public readonly float $valueVat,
        public readonly float $rateVat,
        public readonly float $total,
        public readonly string $note,
        public readonly ?int $createdByUserId
    ) {}
}
