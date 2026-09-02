<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')
                    ->label('Email')->searchable()->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('projects_count')->label('Projects')->counts('projects')->sortable(),
                TextColumn::make('created_at')->label('Created')->dateTime('d/m/Y')->sortable()->toggleable(),
                TextColumn::make('timezone')->label('Time zone')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')->label('Email verified')->dateTime('d/m/Y H:i')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Updated')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleActive')
                    ->label(fn (User $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (User $record): Heroicon => $record->is_active ? Heroicon::OutlinedNoSymbol : Heroicon::OutlinedCheckCircle)
                    ->color(fn (User $record) => $record->is_active ? Color::Red : Color::Green)
                    ->requiresConfirmation(fn (User $record): bool => $record->is_active)
                    ->modalDescription('An inactive user can no longer access the app or their assigned projects.')
                    ->action(fn (User $record) => $record->update(['is_active' => ! $record->is_active]))
                    ->visible(fn (User $record): bool => $record->id !== auth()->id()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
