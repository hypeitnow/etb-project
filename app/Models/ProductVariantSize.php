<?php

namespace App\Models;

use Database\Factories\ProductVariantSizeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantSize extends Model
{
    /** @use HasFactory<ProductVariantSizeFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_label',
        'stock_qty',
        'extra_price_grosze',
    ];

    protected function casts(): array
    {
        return [
            'stock_qty' => 'integer',
            'extra_price_grosze' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
