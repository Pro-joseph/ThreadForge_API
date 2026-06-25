<?php

namespace App\Jobs;

use App\Models\GeneratedPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use function Laravel\Ai\agent;

class RepurposeContentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $rawContent,
        public int $userId,
        public int $campaignBlueprintId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = agent(
            instructions: 'You are a social media content transformer. Transform raw content into a structured post.',
            schema: fn($schema) => [
                'hook_propose' => $schema->string()->required(),
                'body_points' => $schema->array()->items($schema->string())->required(),
                'technicalreadabilityscore' => $schema->integer()->required(),
                'suggested_hashtags' => $schema->array()->items($schema->string())->required(),
                'tonecompliancejustification' => $schema->string()->required(),
            ])->prompt(
            prompt: "Transform this content:\n\n".$this->rawContent,
            provider: 'groq',
            model: 'openai/gpt-oss-20b',
        );

        $data = $response->toArray();

        GeneratedPost::create([
            'user_id' => $this->userId,
            'campaign_blueprint_id' => $this->campaignBlueprintId,
            'raw_content' => $this->rawContent,
            'hook_propose' => $data['hook_propose'] ?? null,
            'body_points' => $data['body_points'] ?? [],
            'technical_readability_score' => $data['technicalreadabilityscore'] ?? null,
            'suggested_hashtags' => $data['suggested_hashtags'] ?? [],
            'tone_compliance_justification' => $data['tonecompliancejustification'] ?? null,
            'status' => 'draft',
        ]);
    }
}
