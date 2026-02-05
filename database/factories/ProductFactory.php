<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency_id' => 1,
            'tax_cost' => fake()->randomFloat(2, 1, 100),
            'manufacturing_cost' => fake()->randomFloat(2, 5, 500),
        ];
    }
}
