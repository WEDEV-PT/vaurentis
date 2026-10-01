<x-filament-panels::page>
    <style>
        .my-projects-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 16px;
        }

        .my-project-card {
            display: flex;
            min-height: 176px;
            flex-direction: column;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            color: #111827;
            padding: 22px;
            text-decoration: none;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .my-project-card:hover,
        .my-project-card:focus-visible {
            border-color: #38527f;
            box-shadow: 0 8px 18px rgba(31, 43, 69, .14);
            outline: none;
            transform: translateY(-2px);
        }

        .my-project-card__name { margin: 0; font-size: 18px; font-weight: 700; line-height: 1.35; }
        .my-project-card__description { margin: 10px 0 0; color: #6b7280; font-size: 14px; line-height: 1.5; }
        .my-project-card__action { margin-top: auto; padding-top: 20px; color: #38527f; font-size: 14px; font-weight: 700; }
        .my-projects-empty { border: 1px dashed #9ca3af; border-radius: 12px; color: #6b7280; padding: 24px; }

        @media (min-width: 768px) {
            .my-projects-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (min-width: 1280px) {
            .my-projects-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
    </style>

    <div class="my-projects-grid">
        @forelse ($this->projects as $project)
            <a href="{{ \App\Filament\Pages\ProjectViewer::getUrl(['project' => $project]) }}" class="my-project-card">
                <p class="my-project-card__name">{{ $project->name }}</p>
                @if ($project->description)
                    <p class="my-project-card__description">{{ $project->description }}</p>
                @endif
                <span class="my-project-card__action">Open project <span aria-hidden="true">→</span></span>
            </a>
        @empty
            <div class="my-projects-empty">No projects assigned.</div>
        @endforelse
    </div>
</x-filament-panels::page>
