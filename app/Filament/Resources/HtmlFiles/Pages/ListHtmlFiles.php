<?php

namespace App\Filament\Resources\HtmlFiles\Pages;

use App\Filament\Resources\HtmlFiles\HtmlFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHtmlFiles extends ListRecords
{
    protected static string $resource = HtmlFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
