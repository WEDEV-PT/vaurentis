<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Support\AuditLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** @var array<int, int> */
    protected array $originalProjectIds = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->originalProjectIds = $this->record->projects()->pluck('projects.id')->all();
    }

    protected function afterSave(): void
    {
        $projectIds = $this->record->projects()->pluck('projects.id')->all();

        if ($this->originalProjectIds !== $projectIds) {
            AuditLogger::record('user.projects_updated', $this->record, [
                'assigned_project_ids' => $projectIds,
            ]);
        }
    }
}
