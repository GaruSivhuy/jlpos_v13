<?php

namespace App\Filament\Resources\Sale;

use App\Filament\Resources\Sale\Pages\ListInvoiceDetails;
use App\Filament\Resources\Sale\Pages\ListInvoices;
use App\Filament\Resources\Sale\Pages\Pos;
use App\Models\Invoice;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

use function Filament\Support\original_request;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'sale';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('global.invoice');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('global.sale');
    }

    public static function getModelLabel(): string
    {
        return __('global.invoice');
    }

    /**
     * Same sidebar entries as the legacy sale menu: POS, Invoice and Invoice Detail.
     *
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        $routeBaseName = static::getRouteBaseName();

        $invoiceItem = collect(parent::getNavigationItems())
            ->first()
            ->icon(Heroicon::OutlinedBanknotes)
            ->sort(static::getNavigationSort() + 1)
            ->isActiveWhen(fn (): bool => original_request()->routeIs($routeBaseName.'.index'));

        return [
            NavigationItem::make(__('global.pos'))
                ->key(static::class.'::pos')
                ->group(static::getNavigationGroup())
                ->icon(Heroicon::OutlinedPlus)
                ->sort(static::getNavigationSort())
                ->url(static::getUrl('pos'), shouldOpenInNewTab: true)
                ->isActiveWhen(fn (): bool => original_request()->routeIs($routeBaseName.'.pos'))
                ->visible(fn (): bool => static::userCan('sale:menu:pos')),
            $invoiceItem,
            NavigationItem::make(__('global.invoice_detail'))
                ->key(static::class.'::details')
                ->group(static::getNavigationGroup())
                ->icon(Heroicon::OutlinedShoppingCart)
                ->sort(static::getNavigationSort() + 2)
                ->url(static::getUrl('details'))
                ->isActiveWhen(fn (): bool => original_request()->routeIs($routeBaseName.'.details'))
                ->visible(fn (): bool => static::userCan('sale:menu:invoice_detail')),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'paymentGateway', 'branch'])
            ->branch();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'details' => ListInvoiceDetails::route('/details'),
            'pos' => Pos::route('/pos'),
        ];
    }

    /**
     * Admins can do everything, other users need the legacy sale permission.
     */
    public static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return $user->is_admin == 1 || $user->can($permission);
    }
}
