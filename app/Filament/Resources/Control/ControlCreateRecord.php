<?php

namespace App\Filament\Resources\Control;

use App\Filament\Resources\BaseCreateRecord;

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
