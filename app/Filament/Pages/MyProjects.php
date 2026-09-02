<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class MyProjects extends Page
{
    protected string $view = 'filament.pages.my-projects';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->is_active && ! auth()->user()->is_admin;
    }

    /** @return Collection<int, Project> */
    public function getProjectsProperty(): Collection
    {
        return auth()->user()
            ->projects()
            ->where('status', 'active')
            ->where('is_published', true)
            ->whereNotNull('html_content')
            ->orderBy('name')
            ->get();
    }
}
