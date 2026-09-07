<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Support\AuditLogger;
use Filament\Http\Middleware\Authenticate;

class AuditAuthenticate extends Authenticate
{
    /** @param array<string> $guards */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->isMethod('GET') && preg_match('#^app/projects/view/(\d+)$#', trim($request->path(), '/'), $matches)) {
            $project = Project::query()->find($matches[1]);

            if ($project) {
                AuditLogger::recordAnonymous('access.unauthenticated', $project, [
                    'reason' => 'login_required',
                ]);
            }
        }

        parent::unauthenticated($request, $guards);
    }
}
