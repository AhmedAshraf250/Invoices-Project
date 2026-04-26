<?php

namespace App\Repositories\Invoices;

use App\Models\Invoice;
use App\Models\Product;
use App\Services\Invoices\DTOs\CreateInvoiceData;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function paginateForIndex(int $perPage = 15, ?string $status = null, bool $onlyTrashed = false);

    /**
     * @return array{total:int,paid:int,partial:int,unpaid:int,overall_total:float}
     */
    public function summaryByStatus(bool $onlyTrashed = false): array;

    public function getOrganizationsForCreate(): Collection;

    public function findProductWithOrganization(int $productId): Product;

    public function createInvoice(CreateInvoiceData $data): Invoice;

    public function updateStatus(
        Invoice $invoice,
        string $status,
        int $statusValue,
        ?float $paidAmount,
        ?string $paymentDate
    ): void;

    public function loadForShow(Invoice $invoice): Invoice;

    public function findWithTrashedById(int $invoiceId): Invoice;

    public function findTrashedById(int $invoiceId): Invoice;

    public function findLatestNumberByPrefix(string $prefix): ?string;

    public function invoiceNumberExists(string $invoiceNumber): bool;

    public function delete(Invoice $invoice): void;

    public function restore(Invoice $invoice): void;

    public function forceDelete(Invoice $invoice): void;
}
