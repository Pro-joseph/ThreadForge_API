<?php

namespace App\Policies;

use App\Models\GeneratedPost;
use App\Models\User;

class GeneratedPostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GeneratedPost $generatedPost): bool
    {
        return $user->id === $generatedPost->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, GeneratedPost $generatedPost): bool
    {
        return $user->id === $generatedPost->user_id;
    }

    public function delete(User $user, GeneratedPost $generatedPost): bool
    {
        return $user->id === $generatedPost->user_id;
    }

    public function restore(User $user, GeneratedPost $generatedPost): bool
    {
        return false;
    }

    public function forceDelete(User $user, GeneratedPost $generatedPost): bool
    {
        return false;
    }
}
