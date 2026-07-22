<?php

namespace Database\Factories;

use App\Models\SportsHall;
use Illuminate\Database\Eloquent\Factories\Factory;

class SportsHallFactory extends Factory
{
    protected $model = SportsHall::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Arena',
        ];
    }
}
