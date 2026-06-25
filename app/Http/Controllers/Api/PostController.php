<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdatePostStatusRequest;
use App\Http\Resources\GeneratedPostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()
        ->generatedPosts()
        ->with('campaignBlueprint')
        ->latest()
        ->paginate(15);

    return response()->json(GeneratedPostResource::collection($posts));

    }

    public function show(string $id): JsonResponse
    {
        $post = request()->user()
        ->generatedPosts()
        ->with('campaignBlueprint')
        ->findOrFail($id);

    return response()->json(new GeneratedPostResource($post));

    }

    public function updateStatus(UpdatePostStatusRequest $request, string $id): JsonResponse
    {
        $post = request()->user()
        ->generatedPosts()
        ->findOrFail($id);

    $post->update(['status' => $request->status]);

    return response()->json(new GeneratedPostResource($post));

    }
}
