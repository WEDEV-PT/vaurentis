<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectSourceFileController extends Controller
{
    public function download(Project $project): StreamedResponse
    {
        $user = request()->user();

        abort_unless($user?->is_active, 403);

        if (! $user->is_admin) {
            abort_unless($project->users()->whereKey($user)->exists(), 403);
            abort_unless($project->status === 'active' && $project->is_published, 404);
        }

        abort_unless(filled($project->source_file_path), 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        abort_unless($disk->exists($project->source_file_path), 404);

        return $disk->download($project->source_file_path, $project->source_filename ?: 'project-data');
    }
}
