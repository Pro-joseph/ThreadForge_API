<?php

namespace App\Providers;

use App\Models\CampaignBlueprint;
use App\Models\GeneratedPost;
use App\Policies\CampaignBlueprintPolicy;
use App\Policies\GeneratedPostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CampaignBlueprint::class => CampaignBlueprintPolicy::class,
        GeneratedPost::class => GeneratedPostPolicy::class,
    ];
}
