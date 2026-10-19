<?php

namespace App\Filament\Auth\MultiFactor;

use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Contracts\Auth\Authenticatable;

class ManagedAppAuthentication extends AppAuthentication
{
    public function isEnabled(Authenticatable $user): bool
    {
        return $user instanceof User
            && $user->two_factor_required
            && $user->two_factor_method === 'app'
            && parent::isEnabled($user);
    }
}
