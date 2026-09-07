<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Support\AuditLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        AuditLogger::record('user.projects_updated', $this->record, [
            'assigned_project_ids' => $this->record->projects()->pluck('projects.id')->all(),
        ]);
    }
}
