<?php

namespace App\Repositories\Invoices\Eloquent;

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Product;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Services\Invoices\DTOs\CreateInvoiceData;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function paginateForIndex(int $perPage = 15)
    {
        return Invoice::query()
            ->with(['organization:id,name', 'productModel:id,name'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function getOrganizationsForCreate(): Collection
    {
        return Organization::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    public function findProductWithOrganization(int $productId): Product
    {
        return Product::query()
            ->with('organization:id,commission_rate')
            ->findOrFail($productId);
    }

    public function createInvoice(CreateInvoiceData $data): Invoice
    {
        return Invoice::query()->create([
            'invoice_number' => $data->invoiceNumber,
            'external_invoice_number' => $data->externalInvoiceNumber,
            'invoice_date' => $data->invoiceDate,
            'due_date' => $data->dueDate,
            'product' => $data->product->name,
            'product_id' => $data->product->id,
            'organization_id' => $data->organizationId,
            'amount_collection' => $data->amountCollection,
            'commission_rate' => $data->commissionRate,
            'amount_commission' => $data->amountCommission,
            'discount' => $data->discountAmount,
            'discount_type' => $data->discountType,
            'discount_value' => $data->discountValue,
            'discount_amount' => $data->discountAmount,
            'value_vat' => $data->valueVat,
            'rate_vat' => $data->rateVat,
            'total' => $data->total,
            'paid_amount' => 0,
            'status' => Invoice::STATUS_UNPAID,
            'status_value' => Invoice::STATUS_VALUE_UNPAID,
            'note' => $data->note,
            'created_by_user_id' => $data->createdByUserId,
        ]);
    }

    public function updateStatus(
        Invoice $invoice,
        string $status,
        int $statusValue,
        ?float $paidAmount,
        ?string $paymentDate
    ): void {
        $invoice->update([
            'status' => $status,
            'status_value' => $statusValue,
            'paid_amount' => $paidAmount,
            'payment_date' => $paymentDate,
        ]);
    }

    public function loadForShow(Invoice $invoice): Invoice
    {
        $invoice->load([
            'organization:id,name',
            'productModel:id,name,commission_rate',
            'creator:id,name',
            'statusHistories.changedBy:id,name',
            'attachments.uploader:id,name',
        ]);

        return $invoice;
    }

    public function findLatestNumberByPrefix(string $prefix): ?string
    {
        return Invoice::query()
            ->withTrashed()
            ->where('invoice_number', 'like', $prefix.'%')
            ->latest('id')
            ->value('invoice_number');
    }

    public function invoiceNumberExists(string $invoiceNumber): bool
    {
        return Invoice::query()
            ->withTrashed()
            ->where('invoice_number', $invoiceNumber)
            ->exists();
    }

    public function delete(Invoice $invoice)
    {
        $invoice->delete();
    }
}
