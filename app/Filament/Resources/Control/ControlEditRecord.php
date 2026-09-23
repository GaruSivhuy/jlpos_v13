<?php

namespace App\Filament\Resources\Control;

use App\Filament\Resources\BaseEditRecord;

/**
 * Edit page that stamps the updating user, like the legacy control module did.
 */
abstract class ControlEditRecord extends BaseEditRecord
{
    /**
     * The "updated by" column of the model.
     */
    protected static string $updatedByColumn = 'user_updated';

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data[static::$updatedByColumn] = auth()->id();

        return $data;
    }
}
