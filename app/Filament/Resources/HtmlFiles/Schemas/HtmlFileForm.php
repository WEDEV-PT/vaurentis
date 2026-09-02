<?php

namespace App\Filament\Resources\HtmlFiles\Schemas;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HtmlFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                FileUpload::make('uploaded_html')
                    ->label('Import HTML file')
                    ->acceptedFileTypes(['text/html', 'application/xhtml+xml'])
                    ->disk('local')
                    ->directory('html-imports')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->helperText('Upload an .html file up to 2 MB. Its contents will replace the editor below.'),
                TextInput::make('name')->label('Name')->required()->maxLength(255),
                TextInput::make('path')
                    ->label('File path')
                    ->placeholder('index.html')
                    ->helperText('Relative to the project, for example index.html or pages/contact.html.')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_published')->label('Published')->default(false),
                CodeEditor::make('content')
                    ->label('HTML content')
                    ->language(Language::Html)
                    ->columnSpanFull(),
            ]);
    }
}
