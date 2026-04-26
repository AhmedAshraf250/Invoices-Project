<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\User;
use App\Notifications\InvoiceCreatedNotification;
use App\Repositories\Invoices\InvoiceAttachmentRepositoryInterface;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;
use App\Repositories\Invoices\TransactionManagerInterface;
use App\Services\Invoices\DTOs\CreateInvoiceData;
use App\Services\Invoices\SubServices\InvoiceCalculator;
use App\Services\Invoices\SubServices\InvoiceNumberGenerator;
use App\Services\Invoices\SubServices\InvoiceStatusManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function index(int $perPage = 15, ?string $status = null, bool $onlyTrashed = false)
    {
        return $this->invoiceRepository->paginateForIndex(
            perPage: $perPage,
            status: $status,
            onlyTrashed: $onlyTrashed,
        );
    }

    public function summary(bool $onlyTrashed = false): array
    {
        return $this->invoiceRepository->summaryByStatus($onlyTrashed);
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

        if ($userId !== null) {
            $user = User::query()->find($userId);

            if ($user !== null) {
                $user->notify(new InvoiceCreatedNotification($invoice));
            }
        }

        return $invoice;
    }

    public function show(Invoice $invoice): Invoice
    {
        return $this->invoiceRepository->loadForShow($invoice);
    }

    public function showWithTrashed(int $invoiceId): Invoice
    {
        return $this->invoiceRepository->findWithTrashedById($invoiceId);
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

    public function getAttachmentFileData(Invoice $invoice, InvoiceAttachment $attachment): ?array
    {
        if ($attachment->invoice_id !== $invoice->id) {
            throw (new ModelNotFoundException)->setModel(InvoiceAttachment::class, [$attachment->id]);
        }

        if (! Storage::disk($attachment->disk)->exists($attachment->file_path)) {
            return null;
        }

        return [
            'disk' => $attachment->disk,
            'file_path' => $attachment->file_path,
            'original_name' => $attachment->original_name,
        ];
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

    public function archiveInvoice(Invoice $invoice): void
    {
        $this->deleteInvoice($invoice);
    }

    public function restoreInvoice(int $invoiceId): void
    {
        $invoice = $this->invoiceRepository->findTrashedById($invoiceId);

        $this->invoiceRepository->restore($invoice);
    }

    public function forceDeleteInvoice(int $invoiceId): void
    {
        $this->transactionManager->run(function () use ($invoiceId): void {
            $invoice = $this->invoiceRepository->findTrashedById($invoiceId);

            $this->cleanupInvoiceAttachments($invoice);

            $this->invoiceRepository->forceDelete($invoice);
        });
    }

    private function cleanupInvoiceAttachments(Invoice $invoice): void
    {
        $attachments = $invoice->attachments()->get();

        foreach ($attachments as $attachment) {
            $disk = Storage::disk($attachment->disk);

            if ($disk->exists($attachment->file_path)) {
                $disk->delete($attachment->file_path);
            }

            $this->invoiceAttachmentRepository->delete($attachment);
        }
    }
}
