<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_method',
        'shipping_address',
        'total_grosze',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'total_grosze' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->total_grosze = $this->items->sum(function (CartItem $item): int {
            return $item->unit_price_grosze * $item->qty;
        });
        $this->save();
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function hasPhysicalItems(): bool
    {
        return $this->items->contains(fn (CartItem $item) => $item->product?->is_physical);
    }
}
