<?php

namespace App\Filament\Resources\Control\ChangeProducts\Pages;

use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListChangeProducts extends ListRecords
{
    protected static string $resource = ChangeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.change_product')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.change_product_list');
    }
}
