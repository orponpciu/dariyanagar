<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('User Role')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'admin' => 'Administrator',
                            'editor' => 'Editor',
                            default => 'No Role',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'admin' => 'success',
                            'editor' => 'info',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                SelectFilter::make('role')
                    ->label('Filter By Role')
                    ->options([
                        'admin' => 'Administrator',
                        'editor' => 'Editor',
                    ]),

            ])

            ->recordActions([

                EditAction::make()
                    ->label('Edit User')
                    ->icon('heroicon-o-pencil-square'),

            ])

            // No bulk delete to protect accounts.
            ->toolbarActions([])

            ->defaultSort('created_at', 'desc');
    }
}
