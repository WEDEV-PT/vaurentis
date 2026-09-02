<?php

namespace App\Filament\Resources\HtmlFiles\Pages;

use App\Filament\Resources\HtmlFiles\HtmlFileResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateHtmlFile extends CreateRecord
{
    protected static string $resource = HtmlFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->importUploadedHtml($data);
    }

    private function importUploadedHtml(array $data): array
    {
        $uploadedHtml = $data['uploaded_html'] ?? null;
        unset($data['uploaded_html']);

        if (blank($uploadedHtml)) {
            return $data;
        }

        $data['content'] = Storage::disk('local')->get($uploadedHtml);
        Storage::disk('local')->delete($uploadedHtml);

        return $data;
    }
}
