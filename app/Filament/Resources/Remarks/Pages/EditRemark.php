<?php

namespace App\Filament\Resources\Remarks\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Remarks\RemarkResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRemark extends BaseEditRecord
{
    protected static string $resource = RemarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
