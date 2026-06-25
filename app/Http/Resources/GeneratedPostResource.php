<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneratedPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'campaign_blueprint_id' => $this->campaign_blueprint_id,
        'raw_content' => $this->raw_content,
        'hook_propose' => $this->hook_propose,
        'body_points' => $this->body_points,
        'technical_readability_score' => $this->technical_readability_score,
        'suggested_hashtags' => $this->suggested_hashtags,
        'tone_compliance_justification' => $this->tone_compliance_justification,
        'status' => $this->status,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
 
    }
}
