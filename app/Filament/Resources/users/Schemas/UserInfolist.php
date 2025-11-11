<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->date(),
                
                TextEntry::make('created_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->date()
                    ->placeholder('-'),
            ]);
    }
}
