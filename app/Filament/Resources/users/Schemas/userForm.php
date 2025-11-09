<?php

namespace App\Filament\Resources\users\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;

class userForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                ->required(),
                TextInput::make('email')
                ->required(),
                TextInput::make('password')
                ->required()
                ->password() //  enkripsi password
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
            ]);
    }
}