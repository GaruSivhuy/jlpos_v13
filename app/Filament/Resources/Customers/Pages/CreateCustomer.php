<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\BaseCreateRecord;
use App\Filament\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends BaseCreateRecord
{
    protected static string $resource = CustomerResource::class;
}
