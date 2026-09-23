<?php

namespace App\Filament\Resources\Control\Metrics\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\Metrics\MetricResource;
use Illuminate\Contracts\Support\Htmlable;

class EditMetric extends ControlEditRecord
{
    protected static string $resource = MetricResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.metric');
    }
}
