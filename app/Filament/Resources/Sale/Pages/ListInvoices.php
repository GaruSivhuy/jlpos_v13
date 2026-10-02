<?php

namespace App\Filament\Resources\Sale\Pages;

use App\Filament\Resources\Sale\InvoiceResource;
use App\Filament\Resources\Sale\Tables\InvoicesTable;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public static function authorizeResourceAccess(): void
    {
        parent::authorizeResourceAccess();

        abort_unless(InvoiceResource::userCan('view_any_invoice:invoice'), 403);
    }

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }
    
}
