<?php

namespace App\Filament\Resources\Control\ChangeProducts\Pages;

use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Filament\Resources\Control\ChangeProducts\Schemas\ChangeProductForm;
use App\Filament\Resources\Control\ControlCreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateChangeProduct extends ControlCreateRecord
{
    protected static string $resource = ChangeProductResource::class;

    protected static ?string $updatedByColumn = 'user_update';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $data['submit_status'] = 0;
        $data['total_amount'] = ChangeProductForm::totalAmount($data['qty'], $data['amount']);

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.change_product');
    }
}
