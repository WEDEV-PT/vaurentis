<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\ProjectAccess;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Width;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProjectViewer extends Page
{
    protected string $view = 'filament.pages.project-viewer';

    protected static bool $shouldRegisterNavigation = false;

    protected Width|string|null $maxContentWidth = Width::Full;

    public Project $project;

    public ?string $previousAccessAt = null;

    public string $clickTrackingToken;

    public static function getRoutePath(Panel $panel): string
    {
        return '/projects/view/{project}';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->is_admin || $user?->projects()->exists();
    }

    public function mount(Project $project): void
    {
        $user = auth()->user();

        abort_unless($user->is_active, 403);

        abort_unless(
            $user->is_admin || $project->users()->whereKey($user)->exists(),
            403,
        );

        abort_unless(
            $project->status === 'active' && $project->is_published && filled($project->html_content),
            404,
        );

        $this->previousAccessAt = $project->accesses()
            ->where('user_id', $user->id)
            ->latest('accessed_at')
            ->value('accessed_at');

        ProjectAccess::query()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accessed_at' => now('UTC'),
        ]);

        $this->project = $project;
        $this->clickTrackingToken = Str::random(40);

        session()->put("project-click-tracking.{$project->id}", $this->clickTrackingToken);
    }

    public function getTitle(): string
    {
        return $this->project->name;
    }

    public function getHeading(): ?string
    {
        return null;
    }

    public function getCategoryNamesProperty(): string
    {
        return $this->project->categories()
            ->orderBy('name')
            ->pluck('name')
            ->join(', ') ?: 'No categories';
    }

    public function getPreviousAccessAtForUserProperty(): string
    {
        if (! $this->previousAccessAt) {
            return 'First access';
        }

        return Carbon::parse($this->previousAccessAt)
            ->setTimezone(auth()->user()->timezone ?: 'Europe/Lisbon')
            ->format('d/m/Y H:i');
    }

    public function getTrackedHtmlContentProperty(): string
    {
        $trackingScript = sprintf(
            <<<'HTML'
<script>
document.addEventListener('click', function (event) {
    var root = document.documentElement;
    var width = Math.max(root.scrollWidth, window.innerWidth, 1);
    var height = Math.max(root.scrollHeight, window.innerHeight, 1);

    window.parent.postMessage({
        type: 'vaurentis-project-click',
        projectId: %d,
        token: '%s',
        x: Math.min(10000, Math.max(0, Math.round(((event.clientX + window.scrollX) / width) * 10000))),
        y: Math.min(10000, Math.max(0, Math.round(((event.clientY + window.scrollY) / height) * 10000))),
    }, '*');
}, true);
</script>
HTML,
            $this->project->id,
            $this->clickTrackingToken,
        );

        if (str_contains(strtolower($this->project->html_content), '</body>')) {
            return preg_replace('/<\/body\s*>/i', $trackingScript.'</body>', $this->project->html_content, 1) ?? $this->project->html_content;
        }

        return $this->project->html_content.$trackingScript;
    }
}
