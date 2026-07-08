<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
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

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
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
