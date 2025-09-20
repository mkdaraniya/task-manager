<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Team $team)
    {
        return $team->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Team $team)
    {
        return $team->users()->where('user_id', $user->id)->wherePivot('role', 'owner')->exists() ||
               $team->users()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
    }

    public function delete(User $user, Team $team)
    {
        return $team->user_id === $user->id;
    }
}
