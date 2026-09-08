<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Date & time')
                    ->dateTime('d/m/Y H:i', fn (): string => auth()->user()?->timezone ?: 'Europe/Lisbon')
                    ->sortable(),
                TextColumn::make('actor.name')->label('User')->default('Guest')->searchable()->sortable(),
                TextColumn::make('action')->label('Action')->badge()->searchable(),
                TextColumn::make('target_type')->label('Target type')->badge(),
                TextColumn::make('target_label')->label('Target')->searchable()->sortable(),
                TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
                TextColumn::make('details')
                    ->label('Details')
                    ->formatStateUsing(function ($state, AuditLog $record): string {
                        $details = $record->details;

                        if (is_string($details)) {
                            $details = json_decode($details, true) ?? ['value' => $details];
                        }

                        return collect(is_array($details) ? $details : [])
                            ->map(function ($value, string $key): string {
                                $label = match ($key) {
                                    'reason' => 'Reason',
                                    'changed_fields' => 'Changed fields',
                                    default => Str::headline($key),
                                };

                                $formattedValue = is_array($value)
                                    ? collect($value)
                                        ->map(fn ($item): string => is_string($item) ? Str::headline($item) : (string) $item)
                                        ->join(', ')
                                    : (is_string($value) ? Str::headline($value) : (string) $value);

                                return "{$label}: {$formattedValue}";
                            })
                            ->join(' · ');
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_agent')->label('User agent')->limit(60)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')->options([
                    'project.created' => 'Project created',
                    'project.updated' => 'Project updated',
                    'project.deleted' => 'Project deleted',
                    'project.assignments_updated' => 'Project assignments updated',
                    'user.created' => 'User created',
                    'user.updated' => 'User updated',
                    'user.deleted' => 'User deleted',
                    'user.projects_updated' => 'User projects updated',
                    'category.created' => 'Category created',
                    'category.updated' => 'Category updated',
                    'category.deleted' => 'Category deleted',
                    'access.denied' => 'Access denied',
                    'access.unauthenticated' => 'Unauthenticated access',
                ]),
                SelectFilter::make('project')->relationship('project', 'name')->label('Project'),
                SelectFilter::make('actor')->relationship('actor', 'name')->label('User'),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
