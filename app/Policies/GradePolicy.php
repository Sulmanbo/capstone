<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function view(User $user, Grade $grade): bool
    {
        return $user->role_id === '03';
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->role_id === '03';
    }

    public function finalize(User $user, Grade $grade): bool
    {
        return $user->role_id === '03' && $grade->status === 'submitted';
    }

    public function lock(User $user, Grade $grade): bool
    {
        return $user->role_id === '03' && $grade->status === 'finalized';
    }

    public function unlock(User $user, Grade $grade): bool
    {
        return $user->role_id === '03' && $grade->status === 'locked';
    }
}
