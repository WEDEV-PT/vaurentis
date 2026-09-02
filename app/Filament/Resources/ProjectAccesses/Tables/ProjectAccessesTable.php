<?php

namespace App\Filament\Resources\ProjectAccesses\Tables;

use App\Filament\Pages\ProjectHeatmap;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectAccessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Project')->searchable()->sortable(),
                TextColumn::make('categories.name')->label('Categories')->badge()->separator(','),
                TextColumn::make('users_count')->label('Assigned users')->sortable(),
                TextColumn::make('accesses_count')->label('Total views')->sortable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('clicks_count')->label('Total clicks')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')->label('Description')->limit(50)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('html_filename')->label('HTML file')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Created')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Updated')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categories')->relationship('categories', 'name')->label('Category'),
            ])
            ->recordActions([
                Action::make('heatMap')
                    ->label('Heat map')
                    ->icon(Heroicon::OutlinedMap)
                    ->url(fn (Project $record): string => ProjectHeatmap::getUrl(['project' => $record])),
            ])
            ->defaultSort('accesses_count', 'desc');
    }
}
