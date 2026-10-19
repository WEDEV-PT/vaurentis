<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Illuminate\Http\Request;

class EnsureRequiredMultiFactorAuthenticationIsEnabled extends EnsureMultiFactorAuthenticationIsEnabled
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (! ($user instanceof User) || ! $user->two_factor_required) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
