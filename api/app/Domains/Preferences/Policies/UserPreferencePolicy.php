<?php

namespace App\Domains\Preferences\Policies;

use App\Domains\Identity\User;
use App\Domains\Preferences\Models\UserPreference;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPreferencePolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserPreference $userPreference)
    {
        return $user->id === $userPreference->user_id;
    }

    public function update(User $user, UserPreference $userPreference)
    {
        return $user->id === $userPreference->user_id;
    }
}
