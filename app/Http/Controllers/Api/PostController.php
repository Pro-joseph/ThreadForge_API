<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdatePostStatusRequest;
use App\Http\Resources\GeneratedPostResource;
use App\Models\GeneratedPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Generated Posts
 *
 * View and manage AI-generated social media posts.
 */
class PostController extends Controller
{
    /**
     * List posts
     *
     * Returns a paginated list of generated posts owned by the authenticated user.
     *
     * @authenticated
     *
     * @responseField id The post's unique ID.
     * @responseField campaign_blueprint_id The blueprint used for generation.
     * @responseField raw_content The original content submitted.
     * @responseField hook_propose The AI-generated hook/title.
     * @responseField body_points Array of key points for the post body.
     * @responseField technical_readability_score Readability score (1-10).
     * @responseField suggested_hashtags Array of suggested hashtags.
     * @responseField tone_compliance_justification Explanation of tone match.
     * @responseField status Current status: draft, published, archived.
     * @responseField created_at Timestamp when created.
     * @responseField updated_at Timestamp when last updated.
     *
     * @response status=200 scenario="success" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "campaign_blueprint_id": 1,
     *       "raw_content": "PHP 8.4 introduced...",
     *       "hook_propose": "New in PHP 8.4: Property Hooks!",
     *       "body_points": ["Point 1", "Point 2"],
     *       "technical_readability_score": 7,
     *       "suggested_hashtags": ["#PHP84", "#Coding"],
     *       "tone_compliance_justification": "Professional tone maintained.",
     *       "status": "draft",
     *       "created_at": "2026-06-23T15:00:00.000000Z",
     *       "updated_at": "2026-06-23T15:00:00.000000Z"
     *     }
     *   ],
     *   "meta": { "current_page": 1, "last_page": 1, "total": 1 }
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', GeneratedPost::class);

        $posts = GeneratedPost::where('user_id', $request->user()->id)
            ->with('campaignBlueprint')
            ->latest()
            ->paginate(15);

        return GeneratedPostResource::collection($posts);
    }

    /**
     * Show a post
     *
     * Get the full details of a specific generated post.
     *
     * @authenticated
     *
     * @urlParam id integer required The post's ID. Example: 1
     *
     * @response status=200 scenario="success" {
     *   "id": 1,
     *   "campaign_blueprint_id": 1,
     *   "raw_content": "PHP 8.4 introduced...",
     *   "hook_propose": "New in PHP 8.4: Property Hooks!",
     *   "body_points": ["Point 1", "Point 2"],
     *   "technical_readability_score": 7,
     *   "suggested_hashtags": ["#PHP84", "#Coding"],
     *   "tone_compliance_justification": "Professional tone maintained.",
     *   "status": "draft",
     *   "created_at": "2026-06-23T15:00:00.000000Z",
     *   "updated_at": "2026-06-23T15:00:00.000000Z"
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     * @response status=404 scenario="not found" { "message": "No query results..." }
     */
    public function show(string $postId): JsonResponse
    {
        $post = GeneratedPost::with('campaignBlueprint')->findOrFail($postId);

        $this->authorize('view', $post);

        return response()->json(new GeneratedPostResource($post));
    }

    /**
     * Update post status
     *
     * Change the status of a generated post (e.g., publish, archive).
     *
     * @authenticated
     *
     * @urlParam id integer required The post's ID. Example: 1
     *
     * @bodyParam status string required New status. Allowed: draft, published, archived. Example: published
     *
     * @response status=200 scenario="updated" {
     *   "id": 1,
     *   "status": "published",
     *   ...
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     * @response status=422 scenario="validation error" {
     *   "message": "The selected status is invalid.",
     *   "errors": { "status": ["The selected status is invalid."] }
     * }
     */
    public function updateStatus(UpdatePostStatusRequest $request, string $postId): JsonResponse
    {
        $post = GeneratedPost::findOrFail($postId);

        $this->authorize('update', $post);

        $post->update(['status' => $request->status]);

        return response()->json(new GeneratedPostResource($post));
    }
}
