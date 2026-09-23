<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\BaseCreateRecord;
use App\Filament\Resources\Users\UserResource;

class CreateUser extends BaseCreateRecord
{
    protected static string $resource = UserResource::class;
}
