<?php

namespace App\Filament\Resources\Remarks\Pages;

use App\Filament\Resources\Remarks\RemarkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListRemarks extends ListRecords
{
    protected static string $resource = RemarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label(__("global.create")." ".__("global.remarkable")),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __("global.remarkable_list");
    }
}
