<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceStatusHistory extends Model
{
    protected $fillable = [
        'invoice_id',
        'from_status',
        'from_status_value',
        'to_status',
        'to_status_value',
        'payment_amount',
        'payment_date',
        'note',
        'changed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'from_status_value' => 'integer',
            'to_status_value' => 'integer',
            'payment_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
