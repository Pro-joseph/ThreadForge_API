<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('returns a token with valid credentials', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'secret123',
    ]);

    expect($response->status())->toBe(200);
    expect($response->json('user'))->toHaveKeys(['id', 'name', 'email']);
    expect($response->json('token'))->toBeString();
});

it('returns 401 with invalid credentials', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    expect($response->status())->toBe(401);
    expect($response->json('message'))->toBe('Invalid credentials');
});
