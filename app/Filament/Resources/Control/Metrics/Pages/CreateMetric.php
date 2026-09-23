<?php

namespace App\Filament\Resources\Control\Metrics\Pages;

use App\Filament\Resources\Control\ControlCreateRecord;
use App\Filament\Resources\Control\Metrics\MetricResource;
use Illuminate\Contracts\Support\Htmlable;

class CreateMetric extends ControlCreateRecord
{
    protected static string $resource = MetricResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.create').' '.__('global.metric');
    }
}
