<?php

namespace App\Filament\Resources\Control\OverMoney\Pages;

use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListOverMoney extends ListRecords
{
    protected static string $resource = OverMoneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.over_money')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.over_money_list');
    }
}
