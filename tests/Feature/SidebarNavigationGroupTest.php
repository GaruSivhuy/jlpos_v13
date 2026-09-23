<?php

use App\Filament\Pages\Reports;
use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
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

it('gives every sidebar group an icon so the collapsed sidebar can show a group dropdown', function (string $locale) {
    app()->setLocale($locale);
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $groups = collect(Filament::getNavigation())
        ->filter(fn (NavigationGroup $group) => filled($group->getLabel()));

    expect($groups)->not->toBeEmpty()
        ->and($groups->filter(fn (NavigationGroup $group) => blank($group->getIcon()))->map->getLabel()->all())->toBe([]);
})->with(['en', 'km']);

it('keeps the sale group in its dropdown items', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $sale = collect(Filament::getNavigation())
        ->first(fn (NavigationGroup $group) => $group->getLabel() === __('global.sale'));

    expect($sale)->not->toBeNull()
        ->and($sale->getItems())->not->toBeEmpty();
});

it('renders exchange money, change products and over money as standalone items right after the sale group, in that order', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $navigation = collect(Filament::getNavigation())->values();
    $saleIndex = $navigation->search(fn (NavigationGroup $group) => $group->getLabel() === __('global.sale'));
    $standalone = $navigation[$saleIndex + 1];

    expect(ExchangeMoneyResource::getNavigationGroup())->toBeNull()
        ->and(ChangeProductResource::getNavigationGroup())->toBeNull()
        ->and(OverMoneyResource::getNavigationGroup())->toBeNull()
        ->and($standalone->getLabel())->toBeNull()
        ->and(collect($standalone->getItems())->map->getKey()->all())->toBe([
            ExchangeMoneyResource::class,
            ChangeProductResource::class,
            OverMoneyResource::class,
        ]);

    $keysBeforeSale = $navigation->take($saleIndex)->flatMap(fn (NavigationGroup $group) => collect($group->getItems())->map->getKey());

    expect($keysBeforeSale)->not->toContain(ExchangeMoneyResource::class)
        ->and($keysBeforeSale)->not->toContain(ChangeProductResource::class)
        ->and($keysBeforeSale)->not->toContain(OverMoneyResource::class);
});

it('renders the reports page as a standalone item right after the control group', function () {
    $this->actingAs(User::factory()->create(['is_admin' => 1]));

    $navigation = collect(Filament::getNavigation())->values();
    $controlIndex = $navigation->search(fn (NavigationGroup $group) => $group->getLabel() === __('global.control'));
    $standalone = $navigation[$controlIndex + 1];

    expect(Reports::getNavigationGroup())->toBeNull()
        ->and($standalone->getLabel())->toBeNull()
        ->and(collect($standalone->getItems())->map->getKey()->all())->toBe([
            Reports::class,
        ]);

    $keysBeforeControl = $navigation->take($controlIndex)->flatMap(fn (NavigationGroup $group) => collect($group->getItems())->map->getKey());

    expect($keysBeforeControl)->not->toContain(Reports::class);
});
