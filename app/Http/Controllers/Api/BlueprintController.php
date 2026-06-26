<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blueprint\StoreBlueprintRequest;
use App\Http\Requests\Blueprint\UpdateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use App\Models\CampaignBlueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Campaign Blueprints
 *
 * Blueprints define the rules and constraints for generated social media posts.
 * Each blueprint belongs to a user and specifies tone, audience, hashtag limits, and character limits.
 */
class BlueprintController extends Controller
{
    /**
     * List blueprints
     *
     * Returns all blueprints owned by the authenticated user.
     *
     * @authenticated
     *
     * @responseField id The blueprint's unique ID.
     * @responseField name Display name of the blueprint.
     * @responseField target_audience Intended audience description.
     * @responseField max_hashtags Maximum number of hashtags allowed.
     * @responseField tone Writing tone guideline.
     * @responseField max_characters Maximum character count per post.
     * @responseField posts_count Number of posts using this blueprint.
     * @responseField created_at Timestamp when created.
     * @responseField updated_at Timestamp when last updated.
     *
     * @response status=200 scenario="success" [
     *   {
     *     "id": 1,
     *     "name": "Tech Thought Leader",
     *     "target_audience": "Software developers",
     *     "max_hashtags": 5,
     *     "tone": "Professional but approachable",
     *     "max_characters": 280,
     *     "posts_count": 3,
     *     "created_at": "2026-06-23T15:00:00.000000Z",
     *     "updated_at": "2026-06-23T15:00:00.000000Z"
     *   }
     * ]
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CampaignBlueprint::class);

        $blueprints = CampaignBlueprint::where('user_id', $request->user()->id)
            ->withCount('generatedPosts')
            ->latest()
            ->get();

        return response()->json(BlueprintResource::collection($blueprints));
    }

    /**
     * Create a blueprint
     *
     * Define a new campaign blueprint with tone, audience, and constraint rules.
     *
     * @authenticated
     *
     * @bodyParam name string required A human-readable name. Example: Tech Thought Leader
     * @bodyParam target_audience string required Describe the intended audience. Example: Software developers
     * @bodyParam max_hashtags integer required Max hashtags per post (1-30). Example: 5
     * @bodyParam tone string required Writing tone guideline. Example: Professional but approachable
     * @bodyParam max_characters integer required Max characters per post (1-2800). Example: 280
     *
     * @response status=201 scenario="created" {
     *   "id": 1,
     *   "name": "Tech Thought Leader",
     *   "target_audience": "Software developers",
     *   "max_hashtags": 5,
     *   "tone": "Professional but approachable",
     *   "max_characters": 280,
     *   "created_at": "2026-06-23T15:00:00.000000Z",
     *   "updated_at": "2026-06-23T15:00:00.000000Z"
     * }
     * @response status=422 scenario="validation error" {
     *   "message": "The name field is required.",
     *   "errors": { "name": ["The name field is required."] }
     * }
     */
    public function store(StoreBlueprintRequest $request): JsonResponse
    {
        $this->authorize('create', CampaignBlueprint::class);

        $blueprint = $request->user()->campaignBlueprints()->create(
            $request->validated()
        );

        return response()->json(new BlueprintResource($blueprint), 201);
    }

    /**
     * Show a blueprint
     *
     * Get the details of a specific blueprint.
     *
     * @authenticated
     *
     * @urlParam id integer required The blueprint's ID. Example: 1
     *
     * @response status=200 scenario="success" {
     *   "id": 1,
     *   "name": "Tech Thought Leader",
     *   "target_audience": "Software developers",
     *   "max_hashtags": 5,
     *   "tone": "Professional but approachable",
     *   "max_characters": 280,
     *   "posts_count": 3,
     *   "created_at": "2026-06-23T15:00:00.000000Z",
     *   "updated_at": "2026-06-23T15:00:00.000000Z"
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     * @response status=404 scenario="not found" { "message": "No query results for model [App\\Models\\CampaignBlueprint] 1" }
     */
    public function show(string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::withCount('generatedPosts')->findOrFail($id);

        $this->authorize('view', $blueprint);

        return response()->json(new BlueprintResource($blueprint));
    }

    /**
     * Update a blueprint
     *
     * Modify an existing blueprint. Only owned blueprints can be updated.
     *
     * @authenticated
     *
     * @urlParam id integer required The blueprint's ID. Example: 1
     *
     * @bodyParam name string New display name. Example: Updated Tech Blueprint
     * @bodyParam target_audience string New audience description. Example: Senior developers
     * @bodyParam max_hashtags integer New max hashtags. Example: 3
     * @bodyParam tone string New tone guideline. Example: Casual and friendly
     * @bodyParam max_characters integer New character limit. Example: 140
     *
     * @response status=200 scenario="updated" {
     *   "id": 1,
     *   "name": "Updated Tech Blueprint",
     *   "target_audience": "Senior developers",
     *   "max_hashtags": 3,
     *   "tone": "Casual and friendly",
     *   "max_characters": 140,
     *   "created_at": "2026-06-23T15:00:00.000000Z",
     *   "updated_at": "2026-06-23T16:00:00.000000Z"
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     */
    public function update(UpdateBlueprintRequest $request, string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::findOrFail($id);

        $this->authorize('update', $blueprint);

        $blueprint->update($request->validated());

        return response()->json(new BlueprintResource($blueprint));
    }

    /**
     * Delete a blueprint
     *
     * Soft-delete a blueprint. Only owned blueprints can be deleted.
     *
     * @authenticated
     *
     * @urlParam id integer required The blueprint's ID. Example: 1
     *
     * @response status=204 scenario="deleted" (no content)
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     */
    public function destroy(string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::findOrFail($id);

        $this->authorize('delete', $blueprint);

        $blueprint->delete();

        return response()->json(null, 204);
    }
}
