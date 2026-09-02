<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Name')->required()->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                FileUpload::make('uploaded_html')
                    ->label('Project HTML file')
                    ->acceptedFileTypes(['text/html', 'application/xhtml+xml'])
                    ->disk('local')
                    ->directory('project-html-imports')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Upload an .html file up to 2 MB. It will be displayed to assigned users.')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Active', 'archived' => 'Archived'])
                    ->default('active')
                    ->required(),
                Toggle::make('is_published')
                    ->label('Available to users')
                    ->default(false),
                Select::make('categories')
                    ->label('Categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Select::make('users')
                    ->label('Assigned users')
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }
}
