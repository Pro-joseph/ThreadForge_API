<?php

use App\Jobs\RepurposeContentJob;
use App\Models\CampaignBlueprint;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

it('dispatches the repurpose job and returns 202', function () {
    Queue::fake();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $blueprint = CampaignBlueprint::factory()->for($user)->create();

    $response = $this->postJson('/api/content/repurpose', [
        'raw_content' => 'Test content for repurposing',
        'campaign_blueprint_id' => $blueprint->id,
    ]);

    expect($response->status())->toBe(202);
    expect($response->json('message'))->toBe('Content submitted for processing');

    Queue::assertPushed(RepurposeContentJob::class, function (RepurposeContentJob $job) use ($user, $blueprint) {
        return $job->userId === $user->id
            && $job->campaignBlueprintId === $blueprint->id
            && $job->rawContent === 'Test content for repurposing';
    });
});
