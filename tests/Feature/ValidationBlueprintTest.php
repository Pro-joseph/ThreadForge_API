<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns validation errors when required fields are missing', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/blueprints', []);

    expect($response->status())->toBe(422);
    expect($response->json('errors'))->toHaveKeys(['name', 'tone']);
});
