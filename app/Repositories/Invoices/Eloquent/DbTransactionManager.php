<?php

namespace App\Repositories\Invoices\Eloquent;

use App\Repositories\Invoices\TransactionManagerInterface;
use Illuminate\Support\Facades\DB;

class DbTransactionManager implements TransactionManagerInterface
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
