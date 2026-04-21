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
        Schema::create('invoice_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->unsignedTinyInteger('from_status_value')->nullable();
            $table->string('to_status', 30);
            $table->unsignedTinyInteger('to_status_value');
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Fast retrieval of an invoice timeline sorted by creation date.
            $table->index(['invoice_id', 'created_at']);
            // Fast aggregation/filtering by target state in reporting screens.
            $table->index(['to_status', 'to_status_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_status_histories');
    }
};
