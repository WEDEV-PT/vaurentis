<?php

namespace App\Filament\Resources\Users\Schemas;

use DateTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Name')->required()->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->helperText('Leave empty to keep the current password.'),
                Select::make('timezone')
                    ->label('Time zone')
                    ->options(array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers()))
                    ->default('Europe/Lisbon')
                    ->searchable()
                    ->required(),
                Select::make('projects')
                    ->label('Assigned projects')
                    ->relationship('projects', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active user')
                    ->default(true),
                Toggle::make('two_factor_required')
                    ->label('Require two-factor authentication')
                    ->helperText('The user will be asked for a second verification step when signing in.')
                    ->live(),
                Select::make('two_factor_method')
                    ->label('Two-factor authentication method')
                    ->options([
                        'app' => 'Authentication app',
                        'email' => 'Email verification code',
                    ])
                    ->required(fn (Get $get): bool => $get('two_factor_required'))
                    ->visible(fn (Get $get): bool => $get('two_factor_required'))
                    ->helperText('An authentication app requires the user to scan a QR code on their next sign-in.'),
            ]);
    }
}
