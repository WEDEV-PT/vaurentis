<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($this->projects as $project)
            <a
                href="{{ \App\Filament\Pages\ProjectViewer::getUrl(['project' => $project]) }}"
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-primary-500 hover:shadow-md dark:border-white/10 dark:bg-gray-900"
            >
                <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $project->name }}</p>
                @if ($project->description)
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $project->description }}</p>
                @endif
                <p class="mt-5 text-sm font-medium text-primary-600 dark:text-primary-400">Open project →</p>
            </a>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-white/20 dark:text-gray-400">
                No projects assigned.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
