<?php

namespace App\Policies;

use App\Models\CampaignBlueprint;
use App\Models\User;

class CampaignBlueprintPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CampaignBlueprint $campaignBlueprint): bool
    {
        return $user->id === $campaignBlueprint->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CampaignBlueprint $campaignBlueprint): bool
    {
        return $user->id === $campaignBlueprint->user_id;
    }

    public function delete(User $user, CampaignBlueprint $campaignBlueprint): bool
    {
        return $user->id === $campaignBlueprint->user_id;
    }

    public function restore(User $user, CampaignBlueprint $campaignBlueprint): bool
    {
        return $user->id === $campaignBlueprint->user_id;
    }

    public function forceDelete(User $user, CampaignBlueprint $campaignBlueprint): bool
    {
        return false;
    }
}
