<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
}
