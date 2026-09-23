<?php

namespace App\Filament\Resources\Control\Categories\Pages;

use App\Filament\Resources\Control\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.category')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.category_list');
    }
}
