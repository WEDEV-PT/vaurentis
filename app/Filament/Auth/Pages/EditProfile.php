<?php

namespace App\Filament\Auth\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->components([
                Section::make('Profile')
                    ->description('Update your account details and password.')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(2),
                Section::make('Application branding')
                    ->description('This logo is displayed in the application navigation for every user.')
                    ->schema([
                        FileUpload::make('brand_logo_path')
                            ->label('Application logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('PNG, JPG, SVG or WebP. Maximum file size: 2 MB.'),
                    ])
                    ->visible(fn (): bool => (bool) $this->getUser()->getAttribute('is_admin')),
            ]);
    }
}
