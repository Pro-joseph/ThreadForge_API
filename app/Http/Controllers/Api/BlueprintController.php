<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blueprint\StoreBlueprintRequest;
use App\Http\Requests\Blueprint\UpdateBlueprintRequest;
use App\Http\Resources\BlueprintResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlueprintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $blueprints = $request->user()
            ->campaignBlueprints()
            ->withCount('generatedPosts')
            ->latest()
        ->get();

    return response()->json(BlueprintResource::collection($blueprints));

    }

    public function store(StoreBlueprintRequest $request): JsonResponse
    {
        $blueprint = $request->user()->campaignBlueprints()->create(
        $request->validated()
    );

    return response()->json(new BlueprintResource($blueprint), 201);
    }

    public function show(string $id): JsonResponse
    {
        $blueprint = request()->user()->campaignBlueprints()
        ->withCount('generatedPosts')
        ->findOrFail($id);

    return response()->json(new BlueprintResource($blueprint));

    }

    public function update(UpdateBlueprintRequest $request, string $id): JsonResponse
    {
        $blueprint = request()->user()->campaignBlueprints()->findOrFail($id);
    $blueprint->update($request->validated());

    return response()->json(new BlueprintResource($blueprint));

    }

    public function destroy(string $id): JsonResponse
    {
        $blueprint = request()->user()->campaignBlueprints()->findOrFail($id);
    $blueprint->delete();

    return response()->json(null, 204);

    }
}
