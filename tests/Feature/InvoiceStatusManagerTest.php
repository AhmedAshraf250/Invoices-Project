<?php

use App\Models\Invoice;
use App\Models\InvoiceStatusHistory;
use App\Models\Organization;
use App\Models\User;
use App\Services\Invoices\SubServices\InvoiceStatusManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it updates invoice status and stores history', function () {
    $user = User::factory()->create();
    $organization = Organization::query()->create([
        'name' => 'Test Org',
        'description' => 'Organization for status test',
        'commission_rate' => 0,
        'created_by' => 'Seeder',
    ]);

    $invoice = Invoice::query()->create([
        'invoice_number' => 'INV-2026-000001',
        'invoice_date' => now()->toDateString(),
        'product' => 'Collection Product',
        'organization_id' => $organization->id,
        'amount_collection' => 1000,
        'amount_commission' => 100,
        'discount' => 0,
        'discount_type' => 'fixed',
        'discount_value' => 0,
        'discount_amount' => 0,
        'value_vat' => 5,
        'rate_vat' => 5,
        'total' => 105,
        'status' => Invoice::STATUS_UNPAID,
        'status_value' => Invoice::STATUS_VALUE_UNPAID,
        'created_by_user_id' => $user->id,
    ]);

    $manager = new InvoiceStatusManager;

    $manager->updateStatus(
        invoice: $invoice,
        status: Invoice::STATUS_PAID,
        paymentAmount: 105,
        paymentDate: now()->toDateString(),
        note: 'Payment received',
        userId: $user->id,
    );

    $invoice->refresh();

    expect($invoice->status)->toBe(Invoice::STATUS_PAID)
        ->and($invoice->status_value)->toBe(Invoice::STATUS_VALUE_PAID)
        ->and((float) $invoice->paid_amount)->toBe(105.0);

    $history = InvoiceStatusHistory::query()->where('invoice_id', $invoice->id)->latest('id')->first();

    expect($history)->not->toBeNull()
        ->and($history->from_status)->toBe(Invoice::STATUS_UNPAID)
        ->and($history->to_status)->toBe(Invoice::STATUS_PAID)
        ->and((float) $history->payment_amount)->toBe(105.0);
});
