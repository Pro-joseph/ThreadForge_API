<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignBlueprintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'target_audience' => fake()->sentence(2),
            'max_hashtags' => fake()->numberBetween(1, 10),
            'tone' => fake()->word(),
            'max_characters' => fake()->numberBetween(100, 500),
        ];
    }
}
