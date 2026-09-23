<?php

use App\Filament\Resources\Sale\InvoiceResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // The legacy users table has an is_admin column that the repo migrations do not create.
    if (! Schema::hasColumn('users', 'is_admin')) {
        Schema::table('users', function ($table) {
            $table->boolean('active')->default(true);
            $table->boolean('is_admin')->default(false);
        });
    }

    Filament::setCurrentPanel('admin');
});

it('registers pos, invoice and invoice detail in the sale menu for admins', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $items = collect(InvoiceResource::getNavigationItems())
        ->filter(fn (NavigationItem $item) => $item->isVisible())
        ->sortBy(fn (NavigationItem $item) => $item->getSort())
        ->values();

    expect($items->map->getLabel()->all())->toBe([
        __('global.pos'),
        __('global.invoice'),
        __('global.invoice_detail'),
    ])
        ->and($items[0]->getUrl())->toBe(InvoiceResource::getUrl('pos'))
        ->and($items[0]->shouldOpenUrlInNewTab())->toBeTrue()
        ->and($items[1]->getUrl())->toBe(InvoiceResource::getUrl('index'))
        ->and($items[2]->getUrl())->toBe(InvoiceResource::getUrl('details'));
});

it('marks only the item of the current sale route as active', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $this->app->instance('originalRequest', tap(Request::create('/admin/sale/pos'), function (Request $request) {
        $request->setRouteResolver(fn () => Route::getRoutes()->match($request));
    }));

    $active = collect(InvoiceResource::getNavigationItems())
        ->filter(fn (NavigationItem $item) => $item->isActive())
        ->map->getLabel()
        ->values()
        ->all();

    expect($active)->toBe([__('global.pos')]);
});

it('hides pos and invoice detail from users without the legacy permissions', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 0]));

    $labels = collect(InvoiceResource::getNavigationItems())
        ->filter(fn (NavigationItem $item) => $item->isVisible())
        ->map->getLabel()
        ->values()
        ->all();

    expect($labels)->toBe([__('global.invoice')]);
});
