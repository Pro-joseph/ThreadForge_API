<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blueprint\StoreBlueprintRequest;
use App\Http\Requests\Blueprint\UpdateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use App\Models\CampaignBlueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlueprintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CampaignBlueprint::class);

        $blueprints = CampaignBlueprint::where('user_id', $request->user()->id)
            ->withCount('generatedPosts')
            ->latest()
            ->get();

        return response()->json(BlueprintResource::collection($blueprints));
    }

    public function store(StoreBlueprintRequest $request): JsonResponse
    {
        $this->authorize('create', CampaignBlueprint::class);

        $blueprint = $request->user()->campaignBlueprints()->create(
            $request->validated()
        );

        return response()->json(new BlueprintResource($blueprint), 201);
    }

    public function show(string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::withCount('generatedPosts')->findOrFail($id);

        $this->authorize('view', $blueprint);

        return response()->json(new BlueprintResource($blueprint));
    }

    public function update(UpdateBlueprintRequest $request, string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::findOrFail($id);

        $this->authorize('update', $blueprint);

        $blueprint->update($request->validated());

        return response()->json(new BlueprintResource($blueprint));
    }

    public function destroy(string $id): JsonResponse
    {
        $blueprint = CampaignBlueprint::findOrFail($id);

        $this->authorize('delete', $blueprint);

        $blueprint->delete();

        return response()->json(null, 204);
    }
}
