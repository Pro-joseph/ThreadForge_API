<?php

namespace Database\Factories;

use App\Models\CampaignBlueprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneratedPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'campaign_blueprint_id' => CampaignBlueprint::factory(),
            'raw_content' => fake()->paragraph(),
            'hook_propose' => fake()->sentence(),
            'body_points' => [fake()->sentence(), fake()->sentence()],
            'technical_readability_score' => fake()->numberBetween(1, 10),
            'suggested_hashtags' => ['#'.fake()->word(), '#'.fake()->word()],
            'tone_compliance_justification' => fake()->sentence(),
            'status' => 'draft',
        ];
    }
}
