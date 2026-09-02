<?php

namespace App\Filament\Resources\HtmlFiles\Pages;

use App\Filament\Resources\HtmlFiles\HtmlFileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditHtmlFile extends EditRecord
{
    protected static string $resource = HtmlFileResource::class;

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

        $data['content'] = Storage::disk('local')->get($uploadedHtml);
        Storage::disk('local')->delete($uploadedHtml);

        return $data;
    }
}
