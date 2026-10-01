<x-filament-panels::page>
    <style>
        #project-board.is-page-maximized { position: fixed !important; inset: 4rem 0 0 !important; z-index: 40; border: 0; border-radius: 0; }
        #project-board.is-page-maximized iframe { height: 100% !important; min-height: 0 !important; }
        .project-control-tooltip { position: absolute; top: 50px; right: 0; width: max-content; padding: 6px 8px; border-radius: 6px; background: #111827; color: #ffffff; font-size: 12px; font-weight: 600; opacity: 0; pointer-events: none; transition: opacity .15s ease; visibility: hidden; }
        button:hover .project-control-tooltip, button:focus-visible .project-control-tooltip, .project-control:hover .project-control-tooltip, .project-control:focus-visible .project-control-tooltip { opacity: 1; visibility: visible; }
    </style>
    <div>
        <div style="margin-bottom: 18px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
            <div style="padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff;"><div style="font-size: 12px; color: #6b7280;">Last access</div><div style="margin-top: 7px; font-size: 14px; font-weight: 600; color: #111827;">{{ $this->previousAccessAtForUser }}</div></div>
            <div style="padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff;"><div style="font-size: 12px; color: #6b7280;">Categories</div><div style="margin-top: 7px; font-size: 14px; font-weight: 600; color: #111827;">{{ $this->categoryNames }}</div></div>
        </div>
        <h1 style="margin: 0 0 12px; font-size: 30px; font-weight: 700; color: #111827;">{{ $project->name }}</h1>
        <div id="project-board" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900" style="position: relative; background: white;">
            <iframe id="project-board-frame" data-click-endpoint="{{ route('project-clicks.store', ['project' => $project]) }}" data-csrf-token="{{ csrf_token() }}" title="{{ $project->name }}" srcdoc="{{ $this->trackedHtmlContent }}" sandbox="allow-forms allow-popups allow-scripts" style="display: block; width: 100%; height: calc(100vh - 11rem); min-height: 720px; border: 0;"></iframe>
            <button type="button" title="Maximize project" aria-label="Maximize project" onclick="const board = document.getElementById('project-board'); const maximized = board.classList.toggle('is-page-maximized'); this.title = maximized ? 'Minimize project' : 'Maximize project'; this.setAttribute('aria-label', this.title); this.querySelector('[data-maximize-icon]').style.display = maximized ? 'none' : 'block'; this.querySelector('[data-minimize-icon]').style.display = maximized ? 'block' : 'none'; this.querySelector('[data-control-tooltip]').textContent = maximized ? 'Minimize' : 'Maximize';" style="position: absolute; top: 12px; right: 12px; z-index: 1; display: flex; width: 42px; height: 42px; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #111827; cursor: pointer; box-shadow: 0 1px 3px rgba(0, 0, 0, .2);">
                    <svg data-maximize-icon style="width: 21px; height: 21px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3H3v5m0-5 6 6m7-6h5v5m0-5-6 6M8 21H3v-5m0 5 6-6m7 6h5v-5m0 5-6-6" /></svg>
                    <svg data-minimize-icon style="display: none; width: 21px; height: 21px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4 4 6 6m0-6v6H4m16-6-6 6m6 0h-6V4M4 20l6-6m-6 0h6v6m10 0-6-6m6 0h-6v6" /></svg>
                    <span data-control-tooltip class="project-control-tooltip">Maximize</span>
            </button>
            @if (filled($project->source_file_path))
                @php($sourceExtension = strtolower(pathinfo($project->source_filename, PATHINFO_EXTENSION)))
                @php($downloadLabel = in_array($sourceExtension, ['xlsx', 'xls'], true) ? 'Download Excel' : ($sourceExtension === 'csv' ? 'Download CSV' : 'Download file'))
                <a class="project-control" href="{{ route('projects.source-file.download', ['project' => $project]) }}" title="{{ $downloadLabel }}" aria-label="{{ $downloadLabel }}" style="position: absolute; top: 12px; right: 64px; z-index: 1; display: flex; width: 42px; height: 42px; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #111827; cursor: pointer; box-shadow: 0 1px 3px rgba(0, 0, 0, .2);"><svg style="width: 21px; height: 21px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4m-5 7v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1 1v-2" /></svg><span class="project-control-tooltip">{{ $downloadLabel }}</span></a>
            @endif
        </div>
    </div>
    <script>
        if (! window.vaurentisProjectClickListenerRegistered) { window.vaurentisProjectClickListenerRegistered = true; window.addEventListener('message', function (event) { const frame = document.getElementById('project-board-frame'); const click = event.data; if (event.source !== frame?.contentWindow || click?.type !== 'vaurentis-project-click' || !Number.isInteger(click.x) || !Number.isInteger(click.y)) return; fetch(frame.dataset.clickEndpoint, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': frame.dataset.csrfToken }, body: JSON.stringify({ tracking_token: click.token, x: click.x, y: click.y }) }).catch(function () {}); }); }
    </script>
</x-filament-panels::page>
