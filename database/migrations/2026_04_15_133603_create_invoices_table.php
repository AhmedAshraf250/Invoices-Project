<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->date('invoice_date')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->string('product', 150);
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->decimal('amount_collection', 15, 2)->nullable();
            $table->decimal('amount_commission', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('value_vat', 15, 2)->default(0);
            $table->decimal('rate_vat', 5, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status', 30)->default('unpaid')->index();
            $table->unsignedTinyInteger('status_value')->default(0)->index();
            $table->text('note')->nullable();
            $table->date('payment_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
