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

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('pos')
            //     ->label(__('global.pos'))
            //     ->icon('heroicon-o-plus')
            //     ->url(fn () => InvoiceResource::getUrl('pos'))
            //     ->openUrlInNewTab()
            //     ->visible(fn () => InvoiceResource::userCan('sale:menu:pos')),
            // Action::make('details')
            //     ->label(__('global.invoice_detail'))
            //     ->icon('heroicon-o-shopping-cart')
            //     ->color('gray')
            //     ->url(fn () => InvoiceResource::getUrl('details'))
            //     ->visible(fn () => InvoiceResource::userCan('sale:menu:invoice_detail')),
        ];
    }
}
