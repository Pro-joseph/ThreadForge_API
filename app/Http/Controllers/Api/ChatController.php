<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\ChatRequest;
use App\Models\GeneratedPost;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function chat(ChatRequest $request, string $postId): JsonResponse
    {
        $post = GeneratedPost::findOrFail($postId);

        $this->authorize('view', $post);

        $agent = new Agent(
            name: 'Ghostwriter',
            instructions: 'You are a social media ghostwriter. Help the user refine their post. Use the tools provided to fetch context.',
            model: 'llama-3.3-70b-versatile',
            provider: 'groq',
        );

        // TODO: Register tools (GetCampaignRules, GetPostHistory) here

        $response = $agent->send($request->message, [
            'conversation' => [
                'post_id' => $postId,
            ],
        ]);

        return response()->json([
            'response' => $response->content,
        ]);
    }
}
