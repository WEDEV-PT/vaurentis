<?php

namespace App\Filament\Resources\EmailLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class EmailLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Date & time')
                    ->dateTime('d/m/Y H:i', fn (): string => auth()->user()?->timezone ?: 'Europe/Lisbon')
                    ->sortable(),
                TextColumn::make('to')->label('To')->searchable()->sortable(),
                TextColumn::make('subject')->label('Subject')->searchable()->wrap(),
                TextColumn::make('from')->label('From')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('body')->label('Body')->limit(100)->wrap()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cc')->label('CC')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bcc')->label('BCC')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attachments')->label('Attachments')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('two_factor')
                    ->label('Two-factor authentication emails')
                    ->query(fn ($query) => $query->where('subject', 'like', '%two-factor%')),
            ])
            ->defaultSort('date', 'desc');
    }
}
