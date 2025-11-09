<?php

namespace App\Filament\Resources\users\Pages;

use App\Filament\Resources\users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class Createuser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}