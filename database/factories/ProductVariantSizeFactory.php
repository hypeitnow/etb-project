<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariantSize;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductVariantSize> */
class ProductVariantSizeFactory extends Factory
{
    protected $model = ProductVariantSize::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size_label' => fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'stock_qty' => fake()->numberBetween(0, 50),
            'extra_price_grosze' => fake()->randomElement([0, 0, 0, 500, 1000]),
        ];
    }
}
