<x-filament-panels::page>
    <style>
        #project-preview.is-page-maximized {
            position: fixed !important;
            inset: 4rem 0 0 !important;
            z-index: 40;
            border: 0;
            border-radius: 0;
        }

        #project-preview.is-page-maximized iframe {
            height: 100% !important;
            min-height: 0 !important;
        }
    </style>

    <div style="margin-bottom: 12px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            <span style="display: inline-flex; align-items: center; border-radius: 9999px; background: #e6eaf2; padding: 5px 10px; color: #1f2b45; font-size: 12px; font-weight: 700;">Admin preview</span>
            <span style="color: #6b7280; font-size: 13px;">This preview does not create access or click analytics.</span>
        </div>
        <h1 style="margin: 10px 0 0; font-size: 30px; font-weight: 700; color: #111827;">{{ $project->name }}</h1>
    </div>

    <div
        id="project-preview"
        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900"
        style="position: relative; background: white;"
    >
        <iframe
            title="Preview · {{ $project->name }}"
            srcdoc="{{ $project->html_content }}"
            sandbox="allow-forms allow-popups allow-scripts"
            style="display: block; width: 100%; height: calc(100vh - 13rem); min-height: 720px; border: 0;"
        ></iframe>
        <button
            type="button"
            title="Maximize preview"
            aria-label="Maximize preview"
            onclick="const preview = document.getElementById('project-preview'); const maximized = preview.classList.toggle('is-page-maximized'); this.title = maximized ? 'Restore preview size' : 'Maximize preview'; this.setAttribute('aria-label', this.title);"
            style="position: absolute; top: 12px; right: 12px; z-index: 1; display: flex; width: 42px; height: 42px; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #111827; cursor: pointer; box-shadow: 0 1px 3px rgba(0, 0, 0, .2);"
        >
            <svg style="width: 21px; height: 21px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 3H3v5m0-5 6 6m7-6h5v5m0-5-6 6M8 21H3v-5m0 5 6-6m7 6h5v-5m0 5-6-6" />
            </svg>
        </button>
    </div>
</x-filament-panels::page>
