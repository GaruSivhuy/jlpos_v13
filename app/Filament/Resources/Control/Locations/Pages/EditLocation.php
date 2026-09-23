<?php

namespace App\Filament\Resources\Control\Locations\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\Locations\LocationResource;
use Illuminate\Contracts\Support\Htmlable;

class EditLocation extends ControlEditRecord
{
    protected static string $resource = LocationResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.location');
    }
}
