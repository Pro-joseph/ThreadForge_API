<?php

namespace Tests\Feature;

use App\Models\CampaignBlueprint;
use App\Models\GeneratedPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_flow(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertArrayHasKey('token', $response->json());

        $token = $response->json('token');

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $login->assertStatus(200);
        $this->assertArrayHasKey('token', $login->json());

        $logout = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $logout->assertStatus(200);
        $this->assertEquals('Logged out', $logout->json('message'));
    }

    public function test_register_duplicate_email(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Dup',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_blueprint_crud(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $headers = ['Authorization' => 'Bearer '.$token];

        $create = $this->postJson('/api/blueprints', [
            'name' => 'Test Blueprint',
            'target_audience' => 'Developers',
            'max_hashtags' => 5,
            'tone' => 'Professional',
            'max_characters' => 280,
        ], $headers);

        $create->assertStatus(201);
        $id = $create->json('id');

        $list = $this->getJson('/api/blueprints', $headers);
        $list->assertStatus(200);
        $this->assertCount(1, $list->json());

        $show = $this->getJson("/api/blueprints/{$id}", $headers);
        $show->assertStatus(200);
        $this->assertEquals('Test Blueprint', $show->json('name'));

        $update = $this->putJson("/api/blueprints/{$id}", [
            'name' => 'Updated',
        ], $headers);

        $update->assertStatus(200);
        $this->assertEquals('Updated', $update->json('name'));

        $delete = $this->deleteJson("/api/blueprints/{$id}", [], $headers);
        $delete->assertStatus(204);
    } 

    public function test_blueprint_policy(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $blueprint = CampaignBlueprint::factory()->create([
            'user_id' => $owner->id,
        ]);

        $token = $other->createToken('test')->plainTextToken;

        $show = $this->getJson("/api/blueprints/{$blueprint->id}", [
            'Authorization' => 'Bearer '.$token,
        ]);

        $show->assertStatus(403);

        $update = $this->putJson("/api/blueprints/{$blueprint->id}", [
            'name' => 'Hacked',
        ], ['Authorization' => 'Bearer '.$token]);

        $update->assertStatus(403);

        $delete = $this->deleteJson("/api/blueprints/{$blueprint->id}", [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $delete->assertStatus(403);
    }

    public function test_posts_scoped_to_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        CampaignBlueprint::factory()->create(['user_id' => $owner->id]);

        GeneratedPost::factory()->create([
            'user_id' => $owner->id,
            'campaign_blueprint_id' => 1,
        ]);
        GeneratedPost::factory()->create([
            'user_id' => $other->id,
            'campaign_blueprint_id' => 1,
        ]);

        $token = $owner->createToken('test')->plainTextToken;

        $list = $this->getJson('/api/posts', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $list->assertStatus(200);
        $items = $list->json();
        $this->assertCount(1, $items['data']);
        $this->assertEquals($owner->id, $items['data'][0]['user_id']);
    }

    public function test_post_update_status(): void
    {
        $user = User::factory()->create();
        CampaignBlueprint::factory()->create(['user_id' => $user->id]);

        $post = GeneratedPost::factory()->create([
            'user_id' => $user->id,
            'campaign_blueprint_id' => 1,
            'status' => 'draft',
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $update = $this->putJson("/api/posts/{$post->id}/status", [
            'status' => 'posted',
        ], ['Authorization' => 'Bearer '.$token]);

        $update->assertStatus(200);
        $this->assertEquals('posted', $update->json('status'));
    }

    public function test_content_repurpose_dispatches_job(): void
    {
        $user = User::factory()->create();

        $blueprint = CampaignBlueprint::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        \Illuminate\Support\Facades\Bus::fake();

        $response = $this->postJson('/api/content/repurpose', [
            'raw_content' => 'Test content for AI processing.',
            'campaign_blueprint_id' => $blueprint->id,
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(202);
        $this->assertEquals('Content submitted for processing', $response->json('message'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\RepurposeContentJob::class);
    }
}
