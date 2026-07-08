<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Koszulki', 'description' => 'Oficjalne koszulki meczowe i treningowe'],
            ['name' => 'Bluzy', 'description' => 'Bluzy i polary z logo klubu'],
            ['name' => 'Czapki', 'description' => 'Czapki z daszkiem i zimowe'],
            ['name' => 'Bilety', 'description' => 'Bilety na mecze i karnety'],
            ['name' => 'Akcesoria', 'description' => 'Akcesoria klubowe i gadżety'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => str($category['name'])->slug()],
                $category
            );
        }
    }
}
