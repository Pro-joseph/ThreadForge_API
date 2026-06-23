<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\ChatRequest;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function chat(ChatRequest $request, string $postId): JsonResponse
    {
        
    }
}
