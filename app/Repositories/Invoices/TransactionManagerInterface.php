<?php

namespace App\Repositories\Invoices;

interface TransactionManagerInterface
{
    public function run(callable $callback): mixed;
}
