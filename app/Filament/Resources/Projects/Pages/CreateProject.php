<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Support\AuditLogger;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        $uploadedHtml = $data['uploaded_html'] ?? null;
        unset($data['uploaded_html']);

        if (blank($uploadedHtml)) {
            return $data;
        }

        $data['html_content'] = Storage::disk('local')->get($uploadedHtml);
        $data['html_filename'] = basename($uploadedHtml);
        Storage::disk('local')->delete($uploadedHtml);

        return $data;
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'project';
        $slug = $baseSlug;
        $suffix = 2;

        while (Project::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function afterCreate(): void
    {
        AuditLogger::record('project.assignments_updated', $this->record, [
            'assigned_user_ids' => $this->record->users()->pluck('users.id')->all(),
            'category_ids' => $this->record->categories()->pluck('categories.id')->all(),
        ]);
    }
}
