<?php

namespace App\Filament\Resources\Control\ServiceFees\Pages;

use App\Filament\Resources\Control\ServiceFees\ServiceFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListServiceFees extends ListRecords
{
    protected static string $resource = ServiceFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.service_fee')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.service_fee_list');
    }
}
