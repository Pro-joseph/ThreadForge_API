<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedPost extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_blueprint_id',
        'raw_content',
        'hook_propose',
        'body_points',
        'technical_readability_score',
        'suggested_hashtags',
        'tone_compliance_justification',
        'status',
    ];

    protected $casts = [
        'body_points' => 'array',
        'suggested_hashtags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaignBlueprint(): BelongsTo
    {
        return $this->belongsTo(CampaignBlueprint::class);
    }
}
