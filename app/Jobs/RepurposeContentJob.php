<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
            $response = ai()->provider('groq')->chat()->create(
            messages: [
                ['role' => 'user', 'content' => "Transform this raw content into a social media post following the rules. Return JSON with hook_propose (max 280 chars), body_points (array of strings), technicalreadabilityscore (0-100), suggested_hashtags (array of strings), tonecompliancejustification (string).\n\nRaw content:\n" . $this->rawContent],
            ],
            options: ['model' => 'llama-3.3-70b-versatile'],
            schema: [
                'type' => 'object',
                'properties' => [
                    'hook_propose' => ['type' => 'string'],
                    'body_points' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'technicalreadabilityscore' => ['type' => 'integer'],
                    'suggested_hashtags' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'tonecompliancejustification' => ['type' => 'string'],
                ],
                'required' => ['hook_propose', 'body_points', 'technicalreadabilityscore', 'suggested_hashtags', 'tonecompliancejustification'],
            ],
        );

        $data = $response->data;

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
    
