<?php

namespace App\Filament\Resources\Control\Metrics\Pages;

use App\Filament\Resources\Control\Metrics\MetricResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListMetrics extends ListRecords
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('global.create').' '.__('global.metric')),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('global.metric_list');
    }
}
