<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    public const STATUS_PAID = 'paid';

    public const STATUS_UNPAID = 'unpaid';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_VALUE_PAID = 1;

    public const STATUS_VALUE_UNPAID = 2;

    public const STATUS_VALUE_PARTIAL = 3;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'external_invoice_number',
        'invoice_date',
        'due_date',
        'product',
        'product_id',
        'organization_id',
        'amount_collection',
        'commission_rate',
        'amount_commission',
        'discount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'value_vat',
        'rate_vat',
        'total',
        'paid_amount',
        'status',
        'status_value',
        'note',
        'payment_date',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'amount_collection' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'amount_commission' => 'decimal:2',
            'discount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'value_vat' => 'decimal:2',
            'rate_vat' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'status_value' => 'integer',
        ];
    }

    public static function statusValueFor(string $status): int
    {
        return match ($status) {
            self::STATUS_PAID => self::STATUS_VALUE_PAID,
            self::STATUS_PARTIAL => self::STATUS_VALUE_PARTIAL,
            default => self::STATUS_VALUE_UNPAID,
        };
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function productModel(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(InvoiceStatusHistory::class)->latest('id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InvoiceAttachment::class)->latest('id');
    }
}
