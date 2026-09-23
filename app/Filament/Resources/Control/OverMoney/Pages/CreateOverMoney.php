<?php

namespace App\Filament\Resources\Control\OverMoney\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use Illuminate\Contracts\Support\Htmlable;

class CreateOverMoney extends ControlCreateRecord
{
    protected static string $resource = OverMoneyResource::class;

    protected static ?string $updatedByColumn = 'user_update';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data['over_money_date'] = today();

        return $data;
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.over_money');
    }
}
