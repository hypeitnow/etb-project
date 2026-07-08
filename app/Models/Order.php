<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public const VALID_TRANSITIONS = [
        self::STATUS_PENDING_PAYMENT => [self::STATUS_PAID, self::STATUS_CANCELLED, self::STATUS_FAILED],
        self::STATUS_PAID => [self::STATUS_SHIPPED, self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED => [self::STATUS_CANCELLED],
        self::STATUS_CANCELLED => [],
        self::STATUS_FAILED => [self::STATUS_PENDING_PAYMENT],
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

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::VALID_TRANSITIONS[$this->status] ?? [], true);
    }

    public function availableTransitions(): array
    {
        return self::VALID_TRANSITIONS[$this->status] ?? [];
    }

    public function transitionTo(string $newStatus, User $user, ?string $note = null): void
    {
        if (! $this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from [{$this->status}] to [{$newStatus}].",
            );
        }

        DB::transaction(function () use ($newStatus, $user, $note): void {
            $fromStatus = $this->status;

            $this->update(['status' => $newStatus]);

            $this->statusLogs()->create([
                'from_status' => $fromStatus,
                'to_status' => $newStatus,
                'user_id' => $user->id,
                'note' => $note,
            ]);

            if ($newStatus === self::STATUS_PAID) {
                $this->decrementStock();
            } elseif ($newStatus === self::STATUS_CANCELLED && $fromStatus !== self::STATUS_PENDING_PAYMENT) {
                $this->incrementStock();
            }
        });
    }

    public function decrementStock(): void
    {
        foreach ($this->items as $item) {
            if (! $item->product || ! $item->product->is_physical) {
                continue;
            }

            if ($item->variantSize) {
                $item->variantSize->decrement('stock_qty', $item->qty);
            } else {
                $item->product->decrement('stock_qty', $item->qty);
            }
        }
    }

    public function incrementStock(): void
    {
        foreach ($this->items as $item) {
            if (! $item->product || ! $item->product->is_physical) {
                continue;
            }

            if ($item->variantSize) {
                $item->variantSize->increment('stock_qty', $item->qty);
            } else {
                $item->product->increment('stock_qty', $item->qty);
            }
        }
    }

    public function displayTotal(): string
    {
        return number_format(($this->total_grosze + $this->shipping_grosze) / 100, 2, ',', '').' zł';
    }
}
