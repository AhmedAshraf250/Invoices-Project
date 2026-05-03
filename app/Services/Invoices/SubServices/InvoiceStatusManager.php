<?php

namespace App\Services\Invoices\SubServices;

use App\Models\Invoice;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;
use App\Repositories\Invoices\TransactionManagerInterface;
use Illuminate\Validation\ValidationException;

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
        if ($invoice->status === Invoice::STATUS_PAID) {
            throw ValidationException::withMessages([
                'status' => __('invoices.validation.paid_status_locked'),
            ]);
        }

        if ($status === Invoice::STATUS_UNPAID && (float) $invoice->paid_amount > 0) {
            throw ValidationException::withMessages([
                'status' => __('invoices.validation.unpaid_status_not_allowed_after_payment'),
            ]);
        }

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
            $remainingAmount = max(round($invoiceTotal - $currentPaidAmount, 2), 0);

            $historyPaymentAmount = match ($status) {
                Invoice::STATUS_UNPAID => 0,
                Invoice::STATUS_PAID => $paymentAmount ?? $remainingAmount,
                default => $paymentAmount,
            };

            $invoicePaidAmount = match ($status) {
                Invoice::STATUS_UNPAID => 0,
                Invoice::STATUS_PAID => $invoiceTotal,
                default => $paymentAmount,
            };

            $normalizedPaymentDate = match ($status) {
                Invoice::STATUS_UNPAID => null,
                Invoice::STATUS_PAID, Invoice::STATUS_PARTIAL => $paymentDate ?? now()->toDateString(),
                default => $paymentDate,
            };

            if ($status === Invoice::STATUS_PARTIAL && $fromStatus === Invoice::STATUS_PARTIAL) {
                $historyPaymentAmount = max((float) $historyPaymentAmount, 0);
                $updatedPaidAmount = round($currentPaidAmount + $historyPaymentAmount, 2);

                if ($updatedPaidAmount >= $invoiceTotal) {
                    $toStatus = Invoice::STATUS_PAID;
                    $toStatusValue = Invoice::STATUS_VALUE_PAID;
                    $invoicePaidAmount = $invoiceTotal;
                    $historyPaymentAmount = $remainingAmount;
                } else {
                    $invoicePaidAmount = $updatedPaidAmount;
                }
            }

            $this->invoiceRepository->updateStatus(
                invoice: $invoice,
                status: $toStatus,
                statusValue: $toStatusValue,
                paidAmount: $invoicePaidAmount !== null ? (float) $invoicePaidAmount : null,
                paymentDate: $normalizedPaymentDate
            );

            $this->invoiceStatusHistoryRepository->createStatusChange(
                invoice: $invoice,
                fromStatus: $fromStatus,
                fromStatusValue: $fromStatusValue,
                toStatus: $toStatus,
                toStatusValue: $toStatusValue,
                paymentAmount: $historyPaymentAmount !== null ? (float) $historyPaymentAmount : null,
                paymentDate: $normalizedPaymentDate,
                note: $note,
                changedByUserId: $userId
            );

            return $invoice;
        });
    }
}
