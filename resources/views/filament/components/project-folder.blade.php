@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\ComponentAttributeBag;

    $user = auth()->user();
    $projects = $user && ! $user->is_admin
        ? $user->projects()
            ->where('status', 'active')
            ->where('is_published', true)
            ->whereNotNull('html_content')
            ->orderBy('name')
            ->get()
        : collect();
@endphp

@if ($projects->isNotEmpty())
    <ul class="fi-sidebar-nav-groups">
        <li x-data="{ open: false }" class="fi-sidebar-item">
            <button
                type="button"
                class="fi-sidebar-item-btn w-full"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open.toString()"
            >
                {{
                    \Filament\Support\generate_icon_html(
                        \Filament\Support\Icons\Heroicon::OutlinedFolder,
                        attributes: (new ComponentAttributeBag())->class(['fi-sidebar-item-icon']),
                        size: IconSize::Large,
                    )
                }}

                <span class="fi-sidebar-item-label" style="display: block !important;">My Projects</span>

                <svg
                    x-bind:style="open
                        ? 'margin-left: auto; width: 1rem; height: 1rem; transform: rotate(180deg); transition: transform .15s ease;'
                        : 'margin-left: auto; width: 1rem; height: 1rem; transform: rotate(0deg); transition: transform .15s ease;'"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>

            <ul x-cloak x-show="open" class="fi-sidebar-sub-group-items">
                @foreach ($projects as $project)
                    <x-filament-panels::sidebar.item
                        :first="$loop->first"
                        grouped
                        :last="$loop->last"
                        sub-grouped
                        :url="\App\Filament\Pages\ProjectViewer::getUrl(['project' => $project])"
                    >
                        {{ $project->name }}
                    </x-filament-panels::sidebar.item>
                @endforeach
            </ul>
        </li>
    </ul>
@endif
