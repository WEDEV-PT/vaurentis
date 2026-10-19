<?php

namespace App\Filament\Auth\MultiFactor;

use App\Models\User;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Illuminate\Contracts\Auth\Authenticatable;

class ManagedEmailAuthentication extends EmailAuthentication
{
    public function isEnabled(Authenticatable $user): bool
    {
        return $user instanceof User
            && $user->two_factor_required
            && $user->two_factor_method === 'email'
            && parent::isEnabled($user);
    }
}
