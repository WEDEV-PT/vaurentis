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

    protected ?string $removedHtmlUploadPath = null;

    protected ?string $importedHtmlUploadPath = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['uploaded_source_file'] = $this->record->source_file_path;

        if (filled($this->record->html_content)) {
            $htmlPath = $this->existingHtmlPath();
            Storage::disk('local')->put($htmlPath, $this->record->html_content);
            $data['uploaded_html'] = $htmlPath;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $uploadedHtml = $data['uploaded_html'] ?? null;
        $uploadedSourceFile = $data['uploaded_source_file'] ?? null;
        unset($data['uploaded_html']);
        unset($data['uploaded_source_file']);

        $existingHtmlPath = filled($this->record->html_content) ? $this->existingHtmlPath() : null;

        if ($uploadedHtml !== $existingHtmlPath && filled($uploadedHtml)) {
            $data['html_content'] = Storage::disk('local')->get($uploadedHtml);
            $data['html_filename'] = basename($uploadedHtml);
            $this->removedHtmlUploadPath = $existingHtmlPath;
            $this->importedHtmlUploadPath = $uploadedHtml;
        } elseif (blank($uploadedHtml) && filled($this->record->html_content)) {
            $data['html_content'] = null;
            $data['html_filename'] = null;
            $this->removedHtmlUploadPath = $existingHtmlPath;
        }

        if ($uploadedSourceFile !== $this->record->source_file_path) {
            $this->replacedSourceFilePath = $this->record->source_file_path;
            $data['source_file_path'] = filled($uploadedSourceFile) ? $uploadedSourceFile : null;
            $data['source_filename'] = filled($uploadedSourceFile) ? basename($uploadedSourceFile) : null;
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
        if (filled($this->removedHtmlUploadPath)) {
            Storage::disk('local')->delete($this->removedHtmlUploadPath);
            $this->removedHtmlUploadPath = null;
        }

        if (filled($this->importedHtmlUploadPath)) {
            Storage::disk('local')->delete($this->importedHtmlUploadPath);
            $this->importedHtmlUploadPath = null;
        }

        if (filled($this->replacedSourceFilePath) && $this->replacedSourceFilePath !== $this->record->source_file_path) {
            Storage::disk('local')->delete($this->replacedSourceFilePath);
            $this->replacedSourceFilePath = null;
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

    private function existingHtmlPath(): string
    {
        $filename = basename($this->record->html_filename ?: 'project.html');

        return "project-html-existing/{$this->record->id}/{$filename}";
    }
}
