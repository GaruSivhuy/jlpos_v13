<?php

namespace App\Filament\Resources\Control\Locations\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\Locations\LocationResource;
use Illuminate\Contracts\Support\Htmlable;

class CreateLocation extends ControlCreateRecord
{
    protected static string $resource = LocationResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.location');
    }
}
