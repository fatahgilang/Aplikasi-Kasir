<?php

namespace App\Filament\Resources\users;

use BackedEnum;
use App\Models\User;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\users\Pages\Edituser;
use App\Filament\Resources\users\Pages\Listusers;
use App\Filament\Resources\users\Pages\Createuser;
use App\Filament\Resources\users\Schemas\userForm;
use App\Filament\Resources\users\Tables\usersTable;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $recordTitleAttribute = 'UserResource';

    public static function form(Schema $schema): Schema
    {
        return userForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return usersTable::configure($table);
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
            'index' => Listusers::route('/'),
            'create' => Createuser::route('/create'),
            'edit' => Edituser::route('/{record}/edit'),
        ];
    }
}