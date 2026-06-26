<?php

namespace App\Tools;

use App\Models\GeneratedPost;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class GetPostHistory implements Tool
{
    public function description(): string
    {
        return 'Get recent social media posts by the current user for reference';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->required()->description('Number of recent posts to return'),
        ];
    }

    public function handle(Request $request): string
    {
        $posts = GeneratedPost::where('user_id', auth()->id())
            ->latest()
            ->take($request['limit'])
            ->get(['hook_propose', 'body_points', 'status', 'created_at']);

        return $posts->toJson();
    }
}
