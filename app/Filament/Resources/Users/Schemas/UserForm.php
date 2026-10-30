<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Information')
                    ->description(
                        'Enter the basic account information.'
                    )
                    ->schema([

                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('Enter full name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('Enter email address')
                            ->email()
                            ->required()
                            ->unique(
                                ignoreRecord: true
                            )
                            ->maxLength(255),

                    ])
                    ->columns(2),

                Section::make('Role & Permissions')
                    ->description(
                        'Assign an Administrator or Editor role.'
                    )
                    ->schema([

                        Select::make('role')
                            ->label('User Role')
                            ->options([
                                'admin' => 'Administrator',
                                'editor' => 'Editor',
                            ])
                            ->default('editor')
                            ->required()
                            ->native(false)
                            ->helperText(
                                'Administrator: manages users and website content. Editor: manages permitted content only.'
                            )
                            ->disabled(
                                fn (?User $record): bool =>
                                    $record?->is(auth()->user()) ?? false
                            )
                            ->saved(
                                fn (?User $record): bool =>
                                    ! (
                                        $record?->is(auth()->user()) ?? false
                                    )
                            ),

                    ]),

                Section::make('Account Security')
                    ->description(
                        'Set a secure password for the user.'
                    )
                    ->schema([

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->placeholder(
                                'Enter a secure password'
                            )
                            ->minLength(12)
                            ->maxLength(255)
                            ->confirmed()
                            ->required(
                                fn (string $operation): bool =>
                                    $operation === 'create'
                            )
                            ->dehydrateStateUsing(
                                fn (string $state): string =>
                                    Hash::make($state)
                            )
                            ->saved(
                                fn (?string $state): bool =>
                                    filled($state)
                            )
                            ->helperText(
                                'Minimum 12 characters. Leave blank when editing to keep the existing password.'
                            ),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->placeholder(
                                'Confirm the password'
                            )
                            ->required(
                                fn (string $operation): bool =>
                                    $operation === 'create'
                            )
                            ->saved(false),

                    ])
                    ->columns(2),

            ]);
    }
}
