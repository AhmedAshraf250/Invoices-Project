<?php

namespace App\Services\Invoices\SubServices;

class InvoiceCalculator
{
    public function calculate(
        float $amountCollection,
        float $commissionRate,
        string $discountType,
        float $discountValue,
        float $vatRate
    ) {
        $normalizedCollection = max($amountCollection, 0);
        $normalizedCommissionRate = max($commissionRate, 0);
        $normalizedDiscountValue = max($discountValue, 0);
        $normalizedVatRate = max($vatRate, 0);

        $amountCommission = round($normalizedCollection * ($normalizedCommissionRate / 100), 2);

        $discountAmount = match ($discountType) {
            'percent' => round($amountCommission * ($normalizedDiscountValue / 100), 2),
            default => round($normalizedDiscountValue, 2),
        };

        $discountAmount = min($discountAmount, $amountCommission); // Ensure discount does not exceed commission
        $netCommission = round(max($amountCommission - $discountAmount, 0), 2);
        $valueVat = round($netCommission * ($normalizedVatRate / 100), 2);
        $total = round($netCommission + $valueVat, 2);

        return [
            'amount_commission' => $amountCommission,
            'discount_amount' => $discountAmount,
            'value_vat' => $valueVat,
            'total' => $total,
            'net_commission' => $netCommission,
        ];
    }
}
