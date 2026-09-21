<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Stevebauman\Inventory\InventoryServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    InventoryServiceProvider::class,
];
