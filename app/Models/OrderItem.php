<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_size_id',
        'qty',
        'unit_price_grosze',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price_grosze' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantSize(): BelongsTo
    {
        return $this->belongsTo(ProductVariantSize::class, 'variant_size_id');
    }

    public function subtotal(): int
    {
        return $this->unit_price_grosze * $this->qty;
    }
}
