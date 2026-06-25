<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignBlueprint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'target_audience',
        'max_hashtags',
        'tone',
        'max_characters',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generatedPosts(): HasMany
    {
        return $this->hasMany(GeneratedPost::class);
    }
}
