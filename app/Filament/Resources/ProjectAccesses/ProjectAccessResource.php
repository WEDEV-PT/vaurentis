<?php

namespace App\Filament\Resources\ProjectAccesses;

use App\Filament\Resources\ProjectAccesses\Pages\ListProjectAccesses;
use App\Filament\Resources\ProjectAccesses\Tables\ProjectAccessesTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectAccessResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'analytics';

    protected static ?string $slug = 'analytics';

    public static function canViewAny(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return ProjectAccessesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['users', 'accesses', 'clicks']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectAccesses::route('/'),
        ];
    }
}
