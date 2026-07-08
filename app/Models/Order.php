<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_PAID = 'paid';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_PENDING_PAYMENT => 'Oczekuje na płatność',
        self::STATUS_PAID => 'Opłacone',
        self::STATUS_SHIPPED => 'Wysłane',
        self::STATUS_DELIVERED => 'Dostarczone',
        self::STATUS_CANCELLED => 'Anulowane',
        self::STATUS_FAILED => 'Nieudane',
    ];

    protected $fillable = [
        'user_id',
        'status',
        'total_grosze',
        'shipping_grosze',
        'shipping_method',
        'shipping_address',
        'tracking_number',
        'paid_at',
        'idempotency_key',
        'payment_session_id',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'total_grosze' => 'integer',
            'shipping_grosze' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function displayTotal(): string
    {
        return number_format(($this->total_grosze + $this->shipping_grosze) / 100, 2, ',', '').' zł';
    }
}
