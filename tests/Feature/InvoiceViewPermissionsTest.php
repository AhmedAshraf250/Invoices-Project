<?php

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createInvoiceForViewTest(User $creator): Invoice
{
    $organization = Organization::query()->create([
        'name' => 'Acme Entity',
        'description' => 'Test organization',
        'commission_rate' => 10,
        'created_by' => $creator->email,
    ]);

    $product = Product::query()->create([
        'name' => 'Accounting Package',
        'description' => 'Test product',
        'commission_rate' => 10,
        'organization_id' => $organization->id,
    ]);

    return Invoice::query()->create([
        'invoice_number' => 'INV-TEST-0001',
        'external_invoice_number' => 'EXT-001',
        'invoice_date' => now()->toDateString(),
        'due_date' => now()->addDays(7)->toDateString(),
        'product' => $product->name,
        'product_id' => $product->id,
        'organization_id' => $organization->id,
        'amount_collection' => 1000,
        'commission_rate' => 10,
        'amount_commission' => 100,
        'discount_type' => 'fixed',
        'discount_value' => 0,
        'discount_amount' => 0,
        'value_vat' => 150,
        'rate_vat' => 15,
        'total' => 1150,
        'paid_amount' => 0,
        'status' => Invoice::STATUS_UNPAID,
        'status_value' => Invoice::STATUS_VALUE_UNPAID,
        'note' => 'Test invoice',
        'created_by_user_id' => $creator->id,
    ]);
}

test('invoice index hides actions the user does not have permission for', function () {
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create([
        'status' => User::STATUS_ACTIVE,
    ]);

    $user->givePermissionTo([
        'invoices.list',
        'invoices.create',
        'invoices.view',
        'invoices.delete',
    ]);

    $invoice = createInvoiceForViewTest($user);

    $this->actingAs($user)
        ->get(route('invoices.index'))
        ->assertOk()
        ->assertDontSee(route('invoices.export.excel', ['status' => null, 'archived' => 0]), false)
        ->assertDontSee('archiveInvoiceModal'.$invoice->id, false)
        ->assertSeeText(__('invoices.actions.delete'));
});

test('accountant can access the invoices index page', function () {
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create([
        'status' => User::STATUS_ACTIVE,
    ]);

    $user->assignRole('accountant');

    createInvoiceForViewTest($user);

    $this->actingAs($user)
        ->get(route('invoices.index'))
        ->assertOk();
});

test('invoice show hides restricted status and attachment actions', function () {
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create([
        'status' => User::STATUS_ACTIVE,
    ]);

    $user->givePermissionTo([
        'invoices.list',
        'invoices.create',
        'invoices.view',
        'invoices.delete',
    ]);

    $invoice = createInvoiceForViewTest($user);

    $this->actingAs($user)
        ->get(route('invoices.show', $invoice))
        ->assertOk()
        ->assertSeeText(__('invoices.details.status_update_unavailable'))
        ->assertSeeText(__('invoices.details.attachments_upload_unavailable'))
        ->assertDontSeeText(__('invoices.details.update_status'))
        ->assertDontSeeText(__('invoices.details.upload_button'));
});
