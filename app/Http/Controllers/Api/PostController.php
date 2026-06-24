<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdatePostStatusRequest;
use App\Http\Resources\GeneratedPostResource;
use App\Models\GeneratedPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', GeneratedPost::class);

        $posts = GeneratedPost::where('user_id', $request->user()->id)
            ->with('campaignBlueprint')
            ->latest()
            ->paginate(15);

        return response()->json(GeneratedPostResource::collection($posts));
    }

    public function show(string $id): JsonResponse
    {
        $post = GeneratedPost::with('campaignBlueprint')->findOrFail($id);

        $this->authorize('view', $post);

        return response()->json(new GeneratedPostResource($post));
    }

    public function updateStatus(UpdatePostStatusRequest $request, string $id): JsonResponse
    {
        $post = GeneratedPost::findOrFail($id);

        $this->authorize('update', $post);

        $post->update(['status' => $request->status]);

        return response()->json(new GeneratedPostResource($post));
    }
}
