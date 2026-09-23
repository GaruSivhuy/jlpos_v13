<?php

namespace App\Filament\Resources\Control\MainCategories\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\MainCategories\MainCategoryResource;
use Illuminate\Contracts\Support\Htmlable;

class EditMainCategory extends ControlEditRecord
{
    protected static string $resource = MainCategoryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.main_category');
    }
}
