<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Projects', Project::count())
                ->description('Registered projects')
                ->color('primary'),
            Stat::make('Users', User::count())
                ->description('Contas criadas'),
            Stat::make('Published projects', Project::where('is_published', true)->count())
                ->description('Disponíveis para consulta')
                ->color('success'),
        ];
    }
}
