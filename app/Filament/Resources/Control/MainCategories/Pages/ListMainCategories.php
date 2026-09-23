<?php

namespace App\Filament\Resources\Control\MainCategories\Pages;

use App\Filament\Resources\Control\MainCategories\MainCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListMainCategories extends ListRecords
{
    protected static string $resource = MainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.main_category')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.main_category_list');
    }
}
