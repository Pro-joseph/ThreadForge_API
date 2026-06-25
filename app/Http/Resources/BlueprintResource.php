<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlueprintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
    return [
        'id' => $this->id,
        'name' => $this->name,
        'target_audience' => $this->target_audience,
        'max_hashtags' => $this->max_hashtags,
        'tone' => $this->tone,
        'max_characters' => $this->max_characters,
        'posts_count' => $this->whenCounted('generatedPosts'),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ]; 
    }
}
