<?php

namespace App\Services\Invoices\SubServices;

use App\Models\Invoice;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;
use App\Repositories\Invoices\TransactionManagerInterface;

class InvoiceStatusManager
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceStatusHistoryRepositoryInterface $invoiceStatusHistoryRepository,
        private TransactionManagerInterface $transactionManager
    ) {}

    public function updateStatus(
        Invoice $invoice,
        string $status,
        ?float $paymentAmount,
        ?string $paymentDate,
        ?string $note,
        ?int $userId
    ): Invoice {
        if ($status === $invoice->status && $status !== Invoice::STATUS_PARTIAL) {
            return $invoice;
        }

        return $this->transactionManager->run(function () use ($invoice, $status, $paymentAmount, $paymentDate, $note, $userId): Invoice {
            $fromStatus = $invoice->status;
            $fromStatusValue = $invoice->status_value;
            $toStatus = $status;
            $toStatusValue = Invoice::statusValueFor($toStatus);
            $invoiceTotal = (float) $invoice->total;
            $currentPaidAmount = (float) $invoice->paid_amount;

            $normalizedPaymentAmount = match ($status) {
                Invoice::STATUS_UNPAID => 0,
                Invoice::STATUS_PAID => $paymentAmount ?? $invoiceTotal,
                default => $paymentAmount,
            };

            $normalizedPaymentDate = match ($status) {
                Invoice::STATUS_UNPAID => null,
                Invoice::STATUS_PAID, Invoice::STATUS_PARTIAL => $paymentDate ?? now()->toDateString(),
                default => $paymentDate,
            };

            if ($status === Invoice::STATUS_PARTIAL && $fromStatus === Invoice::STATUS_PARTIAL) {

                $updatedPaidAmount = round($currentPaidAmount + max((float) $normalizedPaymentAmount, 0), 2);

                if ($updatedPaidAmount >= $invoiceTotal) {
                    $toStatus = Invoice::STATUS_PAID;
                    $toStatusValue = Invoice::STATUS_VALUE_PAID;
                    $normalizedPaymentAmount = $invoiceTotal;
                } else {
                    $normalizedPaymentAmount = $updatedPaidAmount;
                }
            }

            $this->invoiceRepository->updateStatus(
                invoice: $invoice,
                status: $toStatus,
                statusValue: $toStatusValue,
                paidAmount: $normalizedPaymentAmount !== null ? (float) $normalizedPaymentAmount : null,
                paymentDate: $normalizedPaymentDate
            );

            $this->invoiceStatusHistoryRepository->createStatusChange(
                invoice: $invoice,
                fromStatus: $fromStatus,
                fromStatusValue: $fromStatusValue,
                toStatus: $toStatus,
                toStatusValue: $toStatusValue,
                paymentAmount: $normalizedPaymentAmount !== null ? (float) $normalizedPaymentAmount : null,
                paymentDate: $normalizedPaymentDate,
                note: $note,
                changedByUserId: $userId
            );

            return $invoice;
        });
    }
}
