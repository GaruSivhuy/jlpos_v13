<?php

namespace App\Filament\Resources\Control\ServiceFees\Pages;

use App\Filament\Resources\Control\ControlEditRecord;
use App\Filament\Resources\Control\ServiceFees\ServiceFeeResource;
use Illuminate\Contracts\Support\Htmlable;

class EditServiceFee extends ControlEditRecord
{
    protected static string $resource = ServiceFeeResource::class;

    protected static string $updatedByColumn = 'user_update';

    public function getTitle(): string|Htmlable
    {
        return __('global.edit').' '.__('global.service_fee');
    }
}
