<?php

namespace App\Filament\Resources\Control\ChangeProducts\Pages;

use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Filament\Resources\Control\ChangeProducts\Schemas\ChangeProductForm;
use App\Filament\Resources\Control\ControlEditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditChangeProduct extends ControlEditRecord
{
    protected static string $resource = ChangeProductResource::class;

    protected static string $updatedByColumn = 'user_update';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);
        $data['total_amount'] = ChangeProductForm::totalAmount($data['qty'], $data['amount']);

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.change_product');
    }
}
