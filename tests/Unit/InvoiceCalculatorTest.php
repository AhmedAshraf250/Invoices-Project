<?php

use App\Services\Invoices\SubServices\InvoiceCalculator;

test('it calculates invoice totals with fixed discount', function () {
    $calculator = new InvoiceCalculator;

    $result = $calculator->calculate(
        amountCollection: 10000,
        commissionRate: 10,
        discountType: 'fixed',
        discountValue: 100,
        vatRate: 5,
    );

    expect($result['amount_commission'])->toBe(1000.0)
        ->and($result['discount_amount'])->toBe(100.0)
        ->and($result['value_vat'])->toBe(45.0)
        ->and($result['total'])->toBe(945.0);
});

test('it calculates invoice totals with percent discount', function () {
    $calculator = new InvoiceCalculator;

    $result = $calculator->calculate(
        amountCollection: 5000,
        commissionRate: 12,
        discountType: 'percent',
        discountValue: 10,
        vatRate: 10,
    );

    expect($result['amount_commission'])->toBe(600.0)
        ->and($result['discount_amount'])->toBe(60.0)
        ->and($result['value_vat'])->toBe(54.0)
        ->and($result['total'])->toBe(594.0);
});
