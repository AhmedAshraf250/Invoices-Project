<?php

namespace App\Providers;

use App\Repositories\Invoices\Eloquent\DbTransactionManager;
use App\Repositories\Invoices\Eloquent\InvoiceAttachmentRepository;
use App\Repositories\Invoices\Eloquent\InvoiceRepository;
use App\Repositories\Invoices\Eloquent\InvoiceStatusHistoryRepository;
use App\Repositories\Invoices\InvoiceAttachmentRepositoryInterface;
use App\Repositories\Invoices\InvoiceRepositoryInterface;
use App\Repositories\Invoices\InvoiceStatusHistoryRepositoryInterface;
use App\Repositories\Invoices\TransactionManagerInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(InvoiceAttachmentRepositoryInterface::class, InvoiceAttachmentRepository::class);
        $this->app->bind(InvoiceStatusHistoryRepositoryInterface::class, InvoiceStatusHistoryRepository::class);
        $this->app->bind(TransactionManagerInterface::class, DbTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
