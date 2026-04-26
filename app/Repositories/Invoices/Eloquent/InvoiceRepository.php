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
    public function paginateForIndex(int $perPage = 15, ?string $status = null, bool $onlyTrashed = false)
    {
        $query = Invoice::query()
            ->with(['organization:id,name', 'productModel:id,name'])
            ->latest('id');

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function summaryByStatus(bool $onlyTrashed = false): array
    {
        $query = Invoice::query();

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'total' => (clone $query)->count(),
            'paid' => (int) ($statusCounts[Invoice::STATUS_PAID] ?? 0),
            'partial' => (int) ($statusCounts[Invoice::STATUS_PARTIAL] ?? 0),
            'unpaid' => (int) ($statusCounts[Invoice::STATUS_UNPAID] ?? 0),
            'overall_total' => (float) ((clone $query)->sum('total') ?? 0),
        ];
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

    public function findWithTrashedById(int $invoiceId): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::query()->withTrashed()
            ->with([
                'organization:id,name',
                'productModel:id,name,commission_rate',
                'creator:id,name,email',
                'attachments.uploader:id,name',
            ])
            ->whereKey($invoiceId)
            ->firstOrFail();

        return $invoice;
    }

    public function findTrashedById(int $invoiceId): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::query()->onlyTrashed()->whereKey($invoiceId)->firstOrFail();

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

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function restore(Invoice $invoice): void
    {
        $invoice->restore();
    }

    public function forceDelete(Invoice $invoice): void
    {
        $invoice->forceDelete();
    }
}
