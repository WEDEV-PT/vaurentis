<?php

namespace App\Filament\Resources\HtmlFiles;

use App\Filament\Resources\HtmlFiles\Pages\CreateHtmlFile;
use App\Filament\Resources\HtmlFiles\Pages\EditHtmlFile;
use App\Filament\Resources\HtmlFiles\Pages\ListHtmlFiles;
use App\Filament\Resources\HtmlFiles\Schemas\HtmlFileForm;
use App\Filament\Resources\HtmlFiles\Tables\HtmlFilesTable;
use App\Models\HtmlFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HtmlFileResource extends Resource
{
    protected static ?string $model = HtmlFile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?string $navigationLabel = 'HTML Files';

    protected static ?string $modelLabel = 'HTML file';

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return HtmlFileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HtmlFilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHtmlFiles::route('/'),
            'create' => CreateHtmlFile::route('/create'),
            'edit' => EditHtmlFile::route('/{record}/edit'),
        ];
    }
}
