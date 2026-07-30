<?php

namespace App\Filament\Resources\Pricelists\Pages;

use App\Filament\Resources\BaseEditRecord;
use App\Filament\Resources\Pricelists\PricelistResource;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class EditPricelist extends BaseEditRecord
{
    protected static string $resource = PricelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            // DeleteAction::make(),
            // ForceDeleteAction::make(),
            // RestoreAction::make(),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['branch_name'] = $this->record->inventory->branch?->name_kh;
        $data['product_name'] = $this->record->inventory?->name_kh." - ". $this->record->inventory?->name;
        $data['metric_name'] = $this->record->metric?->name_kh;
        return parent::mutateFormDataBeforeFill($data);
    }

    public function getTitle(): string|Htmlable
    {
        return __("global.edit")." ".__("global.price_list");
    }
}
