<?php

namespace App\Filament\Resources\Control\Locations\Pages;

use App\Filament\Resources\Control\Locations\LocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.location')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.location_list');
    }
}
