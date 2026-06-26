<?php

namespace App\Tools;

use App\Models\GeneratedPost;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class GetCampaignRules implements Tool
{
    public function description(): string
    {
        return 'Get the campaign blueprint rules (target audience, tone, hashtag limits) for a given post';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'post_id' => $schema->integer()->required()->description('The ID of the post to get rules for'),
        ];
    }

    public function handle(Request $request): string
    {
        $post = GeneratedPost::with('campaignBlueprint')->findOrFail($request['post_id']);
        $bp = $post->campaignBlueprint;

        return json_encode([
            'target_audience' => $bp->target_audience,
            'tone' => $bp->tone,
            'max_hashtags' => $bp->max_hashtags,
            'max_characters' => $bp->max_characters,
        ]);
    }
}
