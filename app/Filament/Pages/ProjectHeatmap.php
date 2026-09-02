<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\Width;

class ProjectHeatmap extends Page
{
    protected string $view = 'filament.pages.project-heatmap';

    protected static bool $shouldRegisterNavigation = false;

    protected Width|string|null $maxContentWidth = Width::Full;

    public Project $project;

    public static function getRoutePath(Panel $panel): string
    {
        return '/analytics/{project}/heat-map';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function mount(Project $project): void
    {
        abort_unless(
            $project->status === 'active' && $project->is_published && filled($project->html_content),
            404,
        );

        $this->project = $project;
    }

    public function getTitle(): string
    {
        return "Heat map · {$this->project->name}";
    }

    public function getHeading(): ?string
    {
        return null;
    }

    public function getTotalClicksProperty(): int
    {
        return $this->project->clicks()->count();
    }

    /** @return array<int, array{x: float, y: float, clicks: int, intensity: float}> */
    public function getHeatmapPointsProperty(): array
    {
        $bins = $this->project->clicks()
            ->selectRaw('FLOOR(click_x / 250) as x_bin, FLOOR(click_y / 250) as y_bin, COUNT(*) as clicks')
            ->groupBy('x_bin', 'y_bin')
            ->get();

        $maximum = max(1, (int) $bins->max('clicks'));

        return $bins
            ->map(fn ($bin): array => [
                'x' => ((int) $bin->x_bin * 2.5) + 1.25,
                'y' => ((int) $bin->y_bin * 2.5) + 1.25,
                'clicks' => (int) $bin->clicks,
                'intensity' => round(((int) $bin->clicks / $maximum), 2),
            ])
            ->values()
            ->all();
    }

    public function getHeatmapHtmlContentProperty(): string
    {
        $script = str_replace(
            '__HEATMAP_POINTS__',
            json_encode($this->heatmapPoints),
            <<<'HTML'
<script>
window.addEventListener('load', function () {
    var points = __HEATMAP_POINTS__;

    if (!points.length) {
        return;
    }

    var body = document.body;
    var root = document.documentElement;
    var layer = document.createElement('div');
    var markers = [];

    layer.setAttribute('aria-hidden', 'true');
    layer.style.cssText = 'position:fixed;inset:0;z-index:2147483647;pointer-events:none;overflow:hidden;';

    points.forEach(function (point) {
        var marker = document.createElement('div');
        var size = 48;
        var hue = Math.round(52 * (1 - point.intensity));
        var coreOpacity = 0.45 + (point.intensity * 0.45);
        var glowOpacity = 0.12 + (point.intensity * 0.28);

        marker.title = point.clicks + ' clicks';
        marker.style.cssText = 'position:absolute;width:' + size + 'px;height:' + size + 'px;transform:translate(-50%,-50%);border-radius:9999px;background:radial-gradient(circle,hsla(' + hue + ',100%,52%,' + coreOpacity + ') 0%,hsla(' + hue + ',100%,52%,' + glowOpacity + ') 48%,transparent 74%);';
        layer.appendChild(marker);
        markers.push({ element: marker, point: point });
    });

    body.appendChild(layer);

    function placeMarkers() {
        var width = Math.max(root.scrollWidth, window.innerWidth);
        var height = Math.max(root.scrollHeight, window.innerHeight);

        markers.forEach(function (marker) {
            marker.element.style.left = ((marker.point.x / 100) * width - window.scrollX) + 'px';
            marker.element.style.top = ((marker.point.y / 100) * height - window.scrollY) + 'px';
        });
    }

    placeMarkers();
    window.addEventListener('resize', placeMarkers);
    document.addEventListener('scroll', placeMarkers, true);
});
</script>
HTML,
        );

        if (str_contains(strtolower($this->project->html_content), '</body>')) {
            return preg_replace('/<\/body\s*>/i', $script.'</body>', $this->project->html_content, 1) ?? $this->project->html_content;
        }

        return $this->project->html_content.$script;
    }
}
