<?php

namespace App\Filament\Resources\Sale\Pages;

use App\Filament\Resources\Sale\InvoiceResource;
use App\Filament\Resources\Sale\Tables\InvoiceDetailsTable;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListInvoiceDetails extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public function table(Table $table): Table
    {
        return InvoiceDetailsTable::configure($table);
    }

    public function getTitle(): string
    {
        return __('global.invoice_detail');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('invoice')
            //     ->label(__('global.invoice'))
            //     ->icon('heroicon-o-banknotes')
            //     ->color('gray')
            //     ->url(fn () => InvoiceResource::getUrl('index')),
        ];
    }
}
