<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectClick;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectClickController extends Controller
{
    public function store(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (! $user->is_active) {
            AuditLogger::record('access.denied', $project, ['reason' => 'inactive_user']);

            abort(403);
        }

        if (! $user->is_admin && ! $project->users()->whereKey($user)->exists()) {
            AuditLogger::record('access.denied', $project, ['reason' => 'project_not_assigned']);

            abort(403);
        }

        abort_unless(
            $project->status === 'active' && $project->is_published && filled($project->html_content),
            404,
        );

        $data = $request->validate([
            'tracking_token' => ['required', 'string', 'size:40'],
            'x' => ['required', 'integer', 'between:0,10000'],
            'y' => ['required', 'integer', 'between:0,10000'],
        ]);

        abort_unless(
            hash_equals(
                (string) $request->session()->get("project-click-tracking.{$project->id}"),
                $data['tracking_token'],
            ),
            403,
        );

        ProjectClick::query()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'click_x' => $data['x'],
            'click_y' => $data['y'],
            'clicked_at' => now('UTC'),
        ]);

        return response()->json(status: 204);
    }
}
