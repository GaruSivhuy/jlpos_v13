<?php

namespace App\Filament\Resources\Control\OverMoney\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use Illuminate\Contracts\Support\Htmlable;

class EditOverMoney extends ControlEditRecord
{
    protected static string $resource = OverMoneyResource::class;

    protected static string $updatedByColumn = 'user_update';

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.over_money');
    }
}
