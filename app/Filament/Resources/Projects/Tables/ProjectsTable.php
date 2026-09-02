<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Project')->searchable()->sortable(),
                TextColumn::make('categories.name')->label('Categories')->badge()->separator(','),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('users_count')->label('Assigned users')->counts('users')->sortable(),
                IconColumn::make('is_published')->label('Available')->boolean(),
                TextColumn::make('updated_at')->label('Updated')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
                TextColumn::make('description')->label('Description')->limit(50)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('html_filename')->label('HTML file')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Created')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
