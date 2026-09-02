<x-filament-panels::page>
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 30px; font-weight: 700; color: #111827;">Heat map · {{ $project->name }}</h1>
            <p style="margin: 6px 0 0; color: #6b7280;">Aggregated click activity. Brighter areas received more clicks.</p>
        </div>

        <a href="{{ \App\Filament\Resources\ProjectAccesses\ProjectAccessResource::getUrl() }}" class="fi-btn fi-btn-color-gray fi-btn-size-sm">
            Back to Analytics
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px;">
        <div style="padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff;">
            <div style="font-size: 12px; color: #6b7280;">Total clicks</div>
            <div style="margin-top: 4px; font-size: 24px; font-weight: 700; color: #111827;">{{ $this->totalClicks }}</div>
        </div>
        <div style="padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff;">
            <div style="font-size: 12px; color: #6b7280;">Heat map scale</div>
            <div style="margin-top: 7px; font-size: 14px; font-weight: 600; color: #111827;">Yellow → orange → red</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
        <iframe
            title="Heat map for {{ $project->name }}"
            srcdoc="{{ $this->heatmapHtmlContent }}"
            sandbox="allow-forms allow-popups allow-scripts"
            style="display: block; width: 100%; height: calc(100vh - 17rem); min-height: 720px; border: 0;"
        ></iframe>
    </div>
</x-filament-panels::page>
