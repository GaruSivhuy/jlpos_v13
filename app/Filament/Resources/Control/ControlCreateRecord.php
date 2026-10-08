<?php

namespace App\Filament\Resources\Control;

use App\Filament\Resources\BaseCreateRecord;
use Illuminate\Support\Arr;

/**
 * Create page that stamps the creating user, like the legacy control module did.
 */
abstract class ControlCreateRecord extends BaseCreateRecord
{
    /**
     * The "updated by" column of the model, null when the table has none.
     */
    protected static ?string $updatedByColumn = 'user_updated';

    /**
     * Fields that keep their value after "create & create another".
     *
     * @var array<int, string>
     */
    protected static array $rememberedFields = ['branch_id', 'payment_type', 'exchange_type', 'service_type'];

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getCreateAnotherFormAction(),
            $this->getSubmitFormAction(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preserveFormDataWhenCreatingAnother(array $data): array
    {
        return Arr::only($data, static::$rememberedFields);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (static::$updatedByColumn !== null) {
            $data[static::$updatedByColumn] = auth()->id();
        }

        return $data;
    }
}
