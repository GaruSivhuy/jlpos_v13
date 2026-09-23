<?php

namespace App\Filament\Resources\Control\Categories\Pages;

use App\Filament\Resources\Control\Categories\CategoryResource;
use App\Filament\Resources\Control\ControlCreateRecord;
use App\Models\MainCategory;
use Illuminate\Contracts\Support\Htmlable;

class CreateCategory extends ControlCreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data['branch_id'] = MainCategory::find($data['main_cat_id'])?->branch_id;

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.category');
    }
}
