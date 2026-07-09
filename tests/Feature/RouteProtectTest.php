<?php

use App\Models\CampaignBlueprint;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns 401 when accessing blueprints without a token', function () {
    $response = $this->getJson('/api/blueprints');

    expect($response->status())->toBe(401);
});

it('returns 200 with the correct JSON structure for authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    CampaignBlueprint::factory()->count(2)->for($user)->create();

    $response = $this->getJson('/api/blueprints');

    expect($response->status())->toBe(200);
    expect($response->json())->each(function ($blueprint) {
        $blueprint->toHaveKeys([
            'id', 'name', 'target_audience', 'max_hashtags',
            'tone', 'max_characters', 'posts_count',
            'created_at', 'updated_at',
        ]);
    });
});
