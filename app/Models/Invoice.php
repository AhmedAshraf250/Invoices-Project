<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'product',
        'organization_id',
        'amount_collection',
        'amount_commission',
        'discount',
        'value_vat',
        'rate_vat',
        'total',
        'status',
        'status_value',
        'note',
        'payment_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'amount_collection' => 'decimal:2',
            'amount_commission' => 'decimal:2',
            'discount' => 'decimal:2',
            'value_vat' => 'decimal:2',
            'rate_vat' => 'decimal:2',
            'total' => 'decimal:2',
            'status_value' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
