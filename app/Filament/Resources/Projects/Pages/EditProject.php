<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Support\AuditLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    /** @var array<int, int> */
    protected array $originalUserIds = [];

    /** @var array<int, int> */
    protected array $originalCategoryIds = [];

    protected ?string $replacedSourceFilePath = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $uploadedHtml = $data['uploaded_html'] ?? null;
        $uploadedSourceFile = $data['uploaded_source_file'] ?? null;
        unset($data['uploaded_html']);
        unset($data['uploaded_source_file']);

        if (filled($uploadedHtml)) {
            $data['html_content'] = Storage::disk('local')->get($uploadedHtml);
            $data['html_filename'] = basename($uploadedHtml);
            Storage::disk('local')->delete($uploadedHtml);
        }

        if (filled($uploadedSourceFile)) {
            $this->replacedSourceFilePath = $this->record->source_file_path;
            $data['source_file_path'] = $uploadedSourceFile;
            $data['source_filename'] = basename($uploadedSourceFile);
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->originalUserIds = $this->record->users()->pluck('users.id')->all();
        $this->originalCategoryIds = $this->record->categories()->pluck('categories.id')->all();
    }

    protected function afterSave(): void
    {
        if (filled($this->replacedSourceFilePath) && $this->replacedSourceFilePath !== $this->record->source_file_path) {
            Storage::disk('local')->delete($this->replacedSourceFilePath);
        }

        $userIds = $this->record->users()->pluck('users.id')->all();
        $categoryIds = $this->record->categories()->pluck('categories.id')->all();

        if ($this->originalUserIds !== $userIds || $this->originalCategoryIds !== $categoryIds) {
            AuditLogger::record('project.assignments_updated', $this->record, [
                'assigned_user_ids' => $userIds,
                'category_ids' => $categoryIds,
            ]);
        }
    }
}
