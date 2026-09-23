<?php

namespace App\Filament\Resources\Control\MainCategories\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\MainCategories\MainCategoryResource;
use Illuminate\Contracts\Support\Htmlable;

class CreateMainCategory extends ControlCreateRecord
{
    protected static string $resource = MainCategoryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.main_category');
    }
}
