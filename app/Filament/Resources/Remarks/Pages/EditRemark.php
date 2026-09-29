<?php

namespace App\Filament\Resources\Remarks\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Remarks\RemarkResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Override;

class EditRemark extends BaseEditRecord
{
    protected static string $resource = RemarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            // ForceDeleteAction::make(),
            // RestoreAction::make(),
        ];
    }

    #[Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['user_updated'] = auth()->user()->id;
        return parent::handleRecordUpdate($record, $data);
    }


}
