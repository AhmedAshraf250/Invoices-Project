<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Repositories\Invoices\InvoiceAttachmentRepositoryInterface;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;
use App\Repositories\Invoices\TransactionManagerInterface;
use App\Services\Invoices\DTOs\CreateInvoiceData;
use App\Services\Invoices\SubServices\InvoiceCalculator;
use App\Services\Invoices\SubServices\InvoiceNumberGenerator;
use App\Services\Invoices\SubServices\InvoiceStatusManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository,
        private InvoiceStatusHistoryRepositoryInterface $invoiceStatusHistoryRepository,
        private TransactionManagerInterface $transactionManager,
        private InvoiceCalculator $invoiceCalculator,
        private InvoiceNumberGenerator $invoiceNumberGenerator,
        private InvoiceStatusManager $invoiceStatusManager
    ) {}

    public function index(int $perPage = 15)
    {
        return $this->invoiceRepository->paginateForIndex($perPage);
    }

    public function organizationsForCreate(): Collection
    {
        return $this->invoiceRepository->getOrganizationsForCreate();
    }

    public function previewCalculation(array $validated): array
    {
        $product = $this->invoiceRepository->findProductWithOrganization((int) $validated['product_id']);
        $commissionRate = (float) ($product->commission_rate ?? $product->organization?->commission_rate ?? 0);

        $calculation = $this->invoiceCalculator->calculate(
            amountCollection: (float) $validated['amount_collection'],
            commissionRate: $commissionRate,
            discountType: (string) $validated['discount_type'],
            discountValue: (float) $validated['discount_value'],
            vatRate: (float) $validated['rate_vat']
        );

        return [
            'commission_rate' => $commissionRate,
            ...$calculation,
        ];
    }

    public function createInvoice(array $validated, ?UploadedFile $attachment, ?int $userId): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = $this->transactionManager->run(function () use ($validated, $attachment, $userId): Invoice {
            $product = $this->invoiceRepository->findProductWithOrganization((int) $validated['product_id']);
            $commissionRate = (float) ($product->commission_rate ?? $product->organization?->commission_rate ?? 0);

            $calculation = $this->invoiceCalculator->calculate(
                amountCollection: (float) $validated['amount_collection'],
                commissionRate: $commissionRate,
                discountType: (string) $validated['discount_type'],
                discountValue: (float) $validated['discount_value'],
                vatRate: (float) $validated['rate_vat']
            );

            $invoiceData = new CreateInvoiceData(
                invoiceNumber: $validated['invoice_number']
                    ?? $this->invoiceNumberGenerator->generate(Carbon::parse($validated['invoice_date'])),
                externalInvoiceNumber: $validated['external_invoice_number'] ?? null,
                invoiceDate: $validated['invoice_date'],
                dueDate: $validated['due_date'] ?? null,
                product: $product,
                organizationId: (int) $validated['organization_id'],
                amountCollection: (float) $validated['amount_collection'],
                commissionRate: $commissionRate,
                amountCommission: (float) $calculation['amount_commission'],
                discountType: (string) $validated['discount_type'],
                discountValue: (float) $validated['discount_value'],
                discountAmount: (float) $calculation['discount_amount'],
                valueVat: (float) $calculation['value_vat'],
                rateVat: (float) $validated['rate_vat'],
                total: (float) $calculation['total'],
                note: filled($validated['note'] ?? null)
                    ? (string) $validated['note']
                    : __('invoices.messages.default_note'),
                createdByUserId: $userId,
            );

            $invoice = $this->invoiceRepository->createInvoice($invoiceData);

            $this->invoiceStatusHistoryRepository->createInitialUnpaid(
                invoice: $invoice,
                note: __('invoices.messages.initial_status_note'),
                changedByUserId: $userId
            );

            if ($attachment !== null) {
                $this->storeAttachment($invoice, $attachment, $userId);
            }

            return $invoice;
        });

        return $invoice;
    }

    public function show(Invoice $invoice): Invoice
    {
        return $this->invoiceRepository->loadForShow($invoice);
    }

    public function updateStatus(Invoice $invoice, array $validated, ?int $userId): Invoice
    {
        return $this->invoiceStatusManager->updateStatus(
            invoice: $invoice,
            status: $validated['status'],
            paymentAmount: isset($validated['payment_amount']) ? (float) $validated['payment_amount'] : null,
            paymentDate: $validated['payment_date'] ?? null,
            note: $validated['note'] ?? null,
            userId: $userId,
        );
    }

    public function storeAttachment(Invoice $invoice, UploadedFile $uploadedFile, ?int $userId): void
    {
        $fileName = now()->format('YmdHis') . '_' . Str::random(12) . '.' . $uploadedFile->getClientOriginalExtension();
        $directory = 'invoices/' . $invoice->id . '/attachments';

        $path = $uploadedFile->storeAs($directory, $fileName, 'public');

        $this->invoiceAttachmentRepository->createForInvoice(
            invoice: $invoice,
            originalName: $uploadedFile->getClientOriginalName(),
            fileName: $fileName,
            filePath: $path,
            disk: 'public',
            mimeType: (string) $uploadedFile->getClientMimeType(),
            fileSize: (int) $uploadedFile->getSize(),
            uploadedByUserId: $userId
        );
    }

    public function downloadAttachment(Invoice $invoice, InvoiceAttachment $attachment): StreamedResponse|RedirectResponse
    {
        abort_unless($attachment->invoice_id === $invoice->id, 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($attachment->disk);

        if (! $disk->exists($attachment->file_path)) {
            return to_route('invoices.show', $invoice)
                ->with('error', __('invoices.messages.attachment_not_found'));
        }

        return $disk->download($attachment->file_path, $attachment->original_name);
    }

    public function destroyAttachment(Invoice $invoice, InvoiceAttachment $attachment): void
    {
        abort_unless($attachment->invoice_id === $invoice->id, 404);

        if (Storage::disk($attachment->disk)->exists($attachment->file_path)) {
            Storage::disk($attachment->disk)->delete($attachment->file_path);
        }

        $this->invoiceAttachmentRepository->delete($attachment);
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $this->invoiceRepository->delete($invoice);
    }
}
