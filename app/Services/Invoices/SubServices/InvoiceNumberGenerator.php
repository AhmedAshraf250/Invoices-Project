<?php

namespace App\Services\Invoices\SubServices;

use App\Repositories\Invoices\InvoiceRepositoryInterface;
use Carbon\CarbonInterface;

class InvoiceNumberGenerator
{
    public function __construct(private InvoiceRepositoryInterface $invoiceRepository) {}

    public function generate(?CarbonInterface $forDate = null): string
    {
        $date = $forDate ?? now();
        $year = $date->format('Y');
        $prefix = "INV-{$year}-";

        $latestNumber = $this->invoiceRepository->findLatestNumberByPrefix($prefix);

        $lastSequence = 0;

        if (is_string($latestNumber)) {
            $parts = explode('-', $latestNumber);
            $lastSequence = (int) ($parts[2] ?? 0);
        }

        do {
            $lastSequence++;
            $number = sprintf('%s%06d', $prefix, $lastSequence);
        } while ($this->invoiceRepository->invoiceNumberExists($number));

        return $number;
    }
}
