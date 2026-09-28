<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Width;

class ProjectPreview extends Page
{
    protected string $view = 'filament.pages.project-preview';

    protected static bool $shouldRegisterNavigation = false;

    protected Width|string|null $maxContentWidth = Width::Full;

    public Project $project;

    public static function getRoutePath(Panel $panel): string
    {
        return '/projects/{project}/preview';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function mount(Project $project): void
    {
        abort_unless(filled($project->html_content), 404);

        $this->project = $project;
    }

    public function getTitle(): string
    {
        return "Preview · {$this->project->name}";
    }

    public function getHeading(): ?string
    {
        return null;
    }
}
