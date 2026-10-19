<?php

namespace App\Filament\Auth\Pages;

use App\Models\User;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Pages\SetUpRequiredMultiFactorAuthentication;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class SetUpRequiredTwoFactorAuthentication extends SetUpRequiredMultiFactorAuthentication
{
    public function getMultiFactorAuthenticationContentComponent(): Component
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return Section::make()
            ->compact()
            ->divided()
            ->secondary()
            ->schema(collect(Filament::getMultiFactorAuthenticationProviders())
                ->filter(fn (MultiFactorAuthenticationProvider $provider): bool => $provider->getId() === $user->two_factor_method)
                ->map(fn (MultiFactorAuthenticationProvider $provider): Component => Group::make($provider->getManagementSchemaComponents())
                    ->statePath($provider->getId()))
                ->all());
    }
}
