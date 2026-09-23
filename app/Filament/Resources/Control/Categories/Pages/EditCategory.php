<?php

namespace App\Filament\Resources\Control\Categories\Pages;

use App\Filament\Resources\Control\Categories\CategoryResource;
use App\Filament\Resources\Control\ControlEditRecord;
use App\Models\MainCategory;
use Illuminate\Contracts\Support\Htmlable;

class EditCategory extends ControlEditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);
        $data['branch_id'] = MainCategory::find($data['main_cat_id'])?->branch_id;

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.category');
    }
}
