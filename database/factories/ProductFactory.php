<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->optional()->paragraph(),
            'price_grosze' => fake()->numberBetween(1999, 29999),
            'category_id' => Category::factory(),
            'stock_qty' => fake()->numberBetween(0, 100),
            'is_physical' => true,
            'is_published' => false,
            'images' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }

    public function digital(): static
    {
        return $this->state(fn (array $attributes) => ['is_physical' => false, 'stock_qty' => 9999]);
    }
}
