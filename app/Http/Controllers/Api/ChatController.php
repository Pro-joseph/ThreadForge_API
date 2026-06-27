<?php

namespace App\Http\Controllers\Api;

use App\Agents\GhostwriterAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\ChatRequest;
use App\Models\GeneratedPost;
use App\Tools\GetCampaignRules;
use App\Tools\GetPostHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @group Ghostwriter Chat
 *
 * Chat with an AI ghostwriter assistant that can help refine and improve your posts.
 * The assistant can look up campaign blueprint rules and review your post history.
 * Conversations are persisted — send a `conversation_id` to continue a previous chat.
 */
class ChatController extends Controller
{
    /**
     * Chat with the ghostwriter
     *
     * Send a message to the AI ghostwriter about a specific post.
     * The assistant has access to the post's content, its campaign blueprint rules,
     * and your recent post history. It will remember the conversation if you provide
     * a `conversation_id` from a previous response.
     *
     * @authenticated
     *
     * @urlParam postId integer required The post's ID. Example: 1
     *
     * @bodyParam message string required Your message or question about the post. Example: Can you make the hook more engaging?
     * @bodyParam conversation_id string optional Continue a previous conversation. Omit to start a new one. Example: 019f0378-c588-7030-a451-182b5e798e38
     *
     * @response status=200 scenario="success" {
     *   "response": "Sure! How about: 'Laravel 11: Less Boilerplate, More Power!'",
     *   "conversation_id": "019f0378-c588-7030-a451-182b5e798e38"
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     * @response status=422 scenario="validation error" {
     *   "message": "The message field is required.",
     *   "errors": { "message": ["The message field is required."] }
     * }
     */
    public function chat(ChatRequest $request, string $postId): JsonResponse
    {
        $post = GeneratedPost::findOrFail($postId);

        $this->authorize('view', $post);

        $postContent = $post->raw_content;
        $postHook = $post->hook_propose;

        $agent = new GhostwriterAgent(
            instructions: 'You are a social media ghostwriter assistant. Help the user refine and improve their post. '.
                'Current post ID: '.$post->id.'. Raw content: "'.$postContent.'"'.($postHook ? ' Current hook: "'.$postHook.'"' : '').
                '. Use the GetCampaignRules tool (with post_id) to check blueprint constraints, and GetPostHistory (with limit) to reference past posts.',
            tools: [new GetCampaignRules, new GetPostHistory],
        );

        $agent->forUser($request->user());

        if ($request->conversation_id) {
            $agent->continue($request->conversation_id, $request->user());
        }

        try {
            $response = $agent->prompt(
                $request->message,
                provider: 'groq',
                model: 'openai/gpt-oss-20b',
            );
        } catch (\Throwable $e) {
            Log::error('Chat AI call failed', [
                'error' => $e->getMessage(),
                'post_id' => $postId,
                'user_id' => $request->user()->id,
            ]);
            return response()->json([
                'message' => 'AI service is temporarily unavailable. Please try again.',
            ], 503);
        }

        return response()->json([
            'response' => $response->text,
            'conversation_id' => $agent->currentConversation(),
        ]);
    }
}
