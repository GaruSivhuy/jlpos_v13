<?php

use App\Filament\Resources\Control\Categories\Pages\CreateCategory;
use App\Filament\Resources\Control\Categories\Pages\ListCategories;
use App\Filament\Resources\Control\ChangeProducts\Pages\CreateChangeProduct;
use App\Filament\Resources\Control\ChangeProducts\Pages\EditChangeProduct;
use App\Filament\Resources\Control\ChangeProducts\Pages\ListChangeProducts;
use App\Filament\Resources\Control\ExchangeMoney\Pages\CreateExchangeMoney;
use App\Filament\Resources\Control\ExchangeMoney\Pages\EditExchangeMoney;
use App\Filament\Resources\Control\ExchangeMoney\Pages\ListExchangeMoney;
use App\Filament\Resources\Control\ExchangeRates\ExchangeRateResource;
use App\Filament\Resources\Control\ExchangeRates\Pages\CreateExchangeRate;
use App\Filament\Resources\Control\ExchangeRates\Pages\ListExchangeRates;
use App\Filament\Resources\Control\Locations\Pages\CreateLocation;
use App\Filament\Resources\Control\MainCategories\Pages\CreateMainCategory;
use App\Filament\Resources\Control\MainCategories\Pages\EditMainCategory;
use App\Filament\Resources\Control\MainCategories\Pages\ListMainCategories;
use App\Filament\Resources\Control\Metrics\Pages\CreateMetric;
use App\Filament\Resources\Control\Metrics\Pages\ListMetrics;
use App\Filament\Resources\Control\OverMoney\Pages\CreateOverMoney;
use App\Filament\Resources\Control\OverMoney\Pages\EditOverMoney;
use App\Filament\Resources\Control\OverMoney\Pages\ListOverMoney;
use App\Filament\Resources\Control\PaymentGateways\Pages\CreatePaymentGateway;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChangeProduct;
use App\Models\ExchangeMoney;
use App\Models\ExchangeRate;
use App\Models\Location;
use App\Models\MainCategory;
use App\Models\Metric;
use App\Models\OverMoney;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/**
 * The legacy tables exist in the real database but not in the repo migrations.
 */
function createControlLegacyTable(string $name, Closure $columns): void
{
    if (! Schema::hasTable($name)) {
        Schema::create($name, $columns);
    }
}

function makeControlChangeProduct(object $test, array $attributes = []): ChangeProduct
{
    return ChangeProduct::create($attributes + [
        'change_product_type' => ChangeProduct::TYPE_ITEM,
        'change_product_date' => today(),
        'location_id' => $test->location->id,
        'inventory_id' => $test->productId,
        'metric_id' => $test->metric->id,
        'qty' => 2,
        'amount' => 1000,
        'total_amount' => 2000,
        'submit_status' => 0,
        'branch_id' => $test->branch->id,
    ]);
}

beforeEach(function () {
    if (! Schema::hasColumn('users', 'is_admin')) {
        Schema::table('users', function ($table) {
            $table->boolean('active')->default(true);
            $table->boolean('is_admin')->default(false);
        });
    }

    createControlLegacyTable('branch', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name_en');
        $table->string('name_kh')->nullable();
        $table->boolean('is_active')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('branchables', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('branch_id');
        $table->string('branchables_type');
        $table->unsignedBigInteger('branchables_id');
        $table->timestamps();
    });

    createControlLegacyTable('main_categories', function (Blueprint $table) {
        $table->increments('id');
        $table->string('cat_name');
        $table->string('cat_name_kh')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_updated')->nullable();
        $table->integer('branch_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createControlLegacyTable('categories', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable();
        $table->integer('main_cat_id')->nullable();
        $table->string('name');
        $table->string('name_kh')->nullable();
        $table->integer('user_updated')->nullable();
        $table->integer('branch_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createControlLegacyTable('metrics', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable();
        $table->integer('user_updated')->nullable();
        $table->string('name');
        $table->integer('qty')->nullable();
        $table->string('name_kh')->nullable();
        $table->integer('branch_id')->nullable();
        $table->string('name_show', 50)->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createControlLegacyTable('metricsables', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('metric_id');
        $table->string('metricsables_type');
        $table->unsignedBigInteger('metricsables_id');
        $table->double('price')->nullable();
        $table->integer('user_id')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('locations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('name_kh')->nullable();
        $table->integer('branch_id')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_updated')->nullable();
        $table->boolean('is_retail')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('exchange_rate', function (Blueprint $table) {
        $table->increments('id');
        $table->double('exchange_rate')->nullable();
        $table->double('exchange_rate_currency')->nullable();
        $table->integer('branch_id')->nullable();
        $table->integer('user_id')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('payment_gateway', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->integer('branch_id')->nullable();
        $table->integer('user_id')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('exchange_money', function (Blueprint $table) {
        $table->increments('id');
        $table->double('amount_exchange')->nullable();
        $table->double('rate_exchange')->nullable();
        $table->date('exchange_date')->nullable();
        $table->integer('exchange_type')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_update')->nullable();
        $table->integer('branch_id')->nullable();
        $table->integer('exchange_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
        $table->double('total_amount')->nullable();
    });

    createControlLegacyTable('over_money', function (Blueprint $table) {
        $table->increments('id');
        $table->double('over_amount')->nullable();
        $table->integer('over_money_type')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_update')->nullable();
        $table->integer('branch_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
        $table->date('over_money_date')->nullable();
    });

    createControlLegacyTable('change_product', function (Blueprint $table) {
        $table->increments('id');
        $table->string('change_description')->nullable();
        $table->integer('change_product_type')->nullable();
        $table->date('change_product_date')->nullable();
        $table->integer('location_id')->nullable();
        $table->integer('inventory_id')->nullable();
        $table->integer('metric_id')->nullable();
        $table->integer('qty')->nullable();
        $table->integer('branch_id')->nullable();
        $table->double('amount')->nullable();
        $table->double('total_amount')->nullable();
        $table->integer('submit_status')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_update')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createControlLegacyTable('inventories', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable();
        $table->integer('category_id')->nullable();
        $table->string('name')->nullable();
        $table->string('name_kh')->nullable();
        $table->integer('branch_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createControlLegacyTable('inventory_stocks', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->nullable();
        $table->integer('inventory_id');
        $table->integer('location_id');
        $table->integer('metric_id')->nullable();
        $table->double('quantity')->default(0);
        $table->string('aisle')->nullable();
        $table->string('row')->nullable();
        $table->string('bin')->nullable();
        $table->timestamps();
    });

    createControlLegacyTable('inventory_stock_movements', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('stock_id');
        $table->integer('user_id')->nullable();
        $table->double('before')->default(0);
        $table->double('after')->default(0);
        $table->double('cost')->default(0);
        $table->string('reason')->nullable();
        $table->timestamps();
    });

    Filament::setCurrentPanel('admin');

    $this->admin = User::factory()->create(['is_admin' => 1]);
    $this->branch = Branch::create(['name_en' => 'Main', 'name_kh' => 'ចម្បង', 'is_active' => true]);
    $this->actingAs($this->admin);
});

describe('main categories', function () {
    it('lists main categories', function () {
        $category = MainCategory::create(['cat_name' => 'Gold', 'cat_name_kh' => 'មាស', 'branch_id' => $this->branch->id]);

        Livewire::test(ListMainCategories::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$category])
            ->assertSee('MCAT-'.str_pad($category->id, 4, '0', STR_PAD_LEFT));
    });

    it('creates a main category stamped with the current user', function () {
        Livewire::test(CreateMainCategory::class)
            ->fillForm(['branch_id' => $this->branch->id, 'cat_name' => 'Gold', 'cat_name_kh' => 'មាស'])
            ->call('create')
            ->assertHasNoFormErrors();

        $category = MainCategory::where('cat_name', 'Gold')->firstOrFail();

        expect($category->branch_id)->toBe($this->branch->id)
            ->and($category->user_id)->toBe($this->admin->id)
            ->and($category->user_updated)->toBe($this->admin->id);
    });

    it('requires names and keeps them unique per branch', function () {
        MainCategory::create(['cat_name' => 'Gold', 'cat_name_kh' => 'មាស', 'branch_id' => $this->branch->id]);
        $otherBranch = Branch::create(['name_en' => 'Other', 'is_active' => true]);

        Livewire::test(CreateMainCategory::class)
            ->fillForm(['branch_id' => $this->branch->id, 'cat_name' => '', 'cat_name_kh' => ''])
            ->call('create')
            ->assertHasFormErrors(['cat_name' => 'required', 'cat_name_kh' => 'required']);

        Livewire::test(CreateMainCategory::class)
            ->fillForm(['branch_id' => $this->branch->id, 'cat_name' => 'Gold', 'cat_name_kh' => 'មាស'])
            ->call('create')
            ->assertHasFormErrors(['cat_name' => 'unique', 'cat_name_kh' => 'unique']);

        Livewire::test(CreateMainCategory::class)
            ->fillForm(['branch_id' => $otherBranch->id, 'cat_name' => 'Gold', 'cat_name_kh' => 'មាស'])
            ->call('create')
            ->assertHasNoFormErrors();
    });

    it('updates a main category, keeping its own name and stamping the updater', function () {
        $category = MainCategory::create(['cat_name' => 'Gold', 'cat_name_kh' => 'មាស', 'branch_id' => $this->branch->id]);

        Livewire::test(EditMainCategory::class, ['record' => $category->getKey()])
            ->fillForm(['cat_name_kh' => 'មាសថ្មី'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($category->refresh()->cat_name_kh)->toBe('មាសថ្មី')
            ->and($category->user_updated)->toBe($this->admin->id);
    });

    it('only lists the branches of a non admin user', function () {
        $otherBranch = Branch::create(['name_en' => 'Other', 'is_active' => true]);
        $own = MainCategory::create(['cat_name' => 'Own', 'cat_name_kh' => 'ខ្លួន', 'branch_id' => $this->branch->id]);
        $foreign = MainCategory::create(['cat_name' => 'Foreign', 'cat_name_kh' => 'ក្រៅ', 'branch_id' => $otherBranch->id]);

        $user = User::factory()->create(['is_admin' => 0]);
        $user->branch()->attach($this->branch->id);
        $this->actingAs($user);

        Livewire::test(ListMainCategories::class)
            ->assertCanSeeTableRecords([$own])
            ->assertCanNotSeeTableRecords([$foreign]);
    });
});

describe('categories', function () {
    it('creates a category in the branch of its main category', function () {
        $mainCategory = MainCategory::create(['cat_name' => 'Gold', 'cat_name_kh' => 'មាស', 'branch_id' => $this->branch->id]);

        Livewire::test(CreateCategory::class)
            ->fillForm(['main_cat_id' => $mainCategory->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន'])
            ->call('create')
            ->assertHasNoFormErrors();

        $category = Category::where('name', 'Ring')->firstOrFail();

        expect($category->branch_id)->toBe($this->branch->id)
            ->and($category->user_id)->toBe($this->admin->id);
    });

    it('keeps category names unique per main category', function () {
        $mainCategory = MainCategory::create(['cat_name' => 'Gold', 'cat_name_kh' => 'មាស', 'branch_id' => $this->branch->id]);
        $otherMainCategory = MainCategory::create(['cat_name' => 'Silver', 'cat_name_kh' => 'ប្រាក់', 'branch_id' => $this->branch->id]);
        Category::create(['main_cat_id' => $mainCategory->id, 'branch_id' => $this->branch->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន']);

        Livewire::test(CreateCategory::class)
            ->fillForm(['main_cat_id' => $mainCategory->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique', 'name_kh' => 'unique']);

        Livewire::test(CreateCategory::class)
            ->fillForm(['main_cat_id' => $otherMainCategory->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន'])
            ->call('create')
            ->assertHasNoFormErrors();
    });

    it('does not delete a category that products use', function () {
        $category = Category::create(['main_cat_id' => 1, 'branch_id' => $this->branch->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន']);
        DB::table('inventories')->insert(['category_id' => $category->id, 'name_kh' => 'Item', 'branch_id' => $this->branch->id]);

        Livewire::test(ListCategories::class)
            ->callAction(TestAction::make('delete')->table($category))
            ->assertNotified();

        expect($category->refresh()->trashed())->toBeFalse();
    });

    it('deletes a category that no product uses', function () {
        $category = Category::create(['main_cat_id' => 1, 'branch_id' => $this->branch->id, 'name' => 'Ring', 'name_kh' => 'ចិញ្ចៀន']);

        Livewire::test(ListCategories::class)
            ->callAction(TestAction::make('delete')->table($category))
            ->assertNotified();

        expect($category->refresh()->trashed())->toBeTrue()
            ->and($category->user_updated)->toBe($this->admin->id);
    });
});

describe('metrics', function () {
    it('creates a metric', function () {
        Livewire::test(CreateMetric::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'Box', 'name_kh' => 'ប្រអប់', 'qty' => 12, 'name_show' => 'Box'])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Metric::where('name', 'Box')->firstOrFail())
            ->qty->toBe(12)
            ->user_id->toBe($this->admin->id);
    });

    it('needs a quantity of at least one per metric', function () {
        Livewire::test(CreateMetric::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'Box', 'name_kh' => 'ប្រអប់', 'qty' => 0, 'name_show' => 'Box'])
            ->call('create')
            ->assertHasFormErrors(['qty']);
    });

    it('does not delete a metric that products use', function () {
        $metric = Metric::create(['name' => 'Box', 'name_kh' => 'ប្រអប់', 'qty' => 1, 'name_show' => 'Box', 'branch_id' => $this->branch->id]);
        DB::table('metricsables')->insert(['metric_id' => $metric->id, 'metricsables_type' => 'products', 'metricsables_id' => 1]);

        Livewire::test(ListMetrics::class)
            ->callAction(TestAction::make('delete')->table($metric))
            ->assertNotified();

        expect($metric->refresh()->trashed())->toBeFalse();
    });

    it('deletes a metric that no product uses', function () {
        $metric = Metric::create(['name' => 'Box', 'name_kh' => 'ប្រអប់', 'qty' => 1, 'name_show' => 'Box', 'branch_id' => $this->branch->id]);

        Livewire::test(ListMetrics::class)
            ->callAction(TestAction::make('delete')->table($metric))
            ->assertNotified();

        expect($metric->refresh()->trashed())->toBeTrue();
    });
});

describe('locations and payment gateways', function () {
    it('creates a location and keeps its names unique per branch', function () {
        Livewire::test(CreateLocation::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'Shop', 'name_kh' => 'ហាង'])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Location::where('name', 'Shop')->firstOrFail()->user_id)->toBe($this->admin->id);

        Livewire::test(CreateLocation::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'Shop', 'name_kh' => 'ហាង'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique', 'name_kh' => 'unique']);
    });

    it('creates a payment gateway and keeps its name unique per branch', function () {
        Livewire::test(CreatePaymentGateway::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'ABA'])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(DB::table('payment_gateway')->where('name', 'ABA')->value('user_id'))->toBe($this->admin->id);

        Livewire::test(CreatePaymentGateway::class)
            ->fillForm(['branch_id' => $this->branch->id, 'name' => 'ABA'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique']);
    });
});

describe('exchange rates', function () {
    it('adds a new rate every time and cannot be edited', function () {
        foreach ([4000, 4100] as $rate) {
            Livewire::test(CreateExchangeRate::class)
                ->fillForm(['branch_id' => $this->branch->id, 'exchange_rate' => $rate, 'exchange_rate_currency' => 1])
                ->call('create')
                ->assertHasNoFormErrors();
        }

        $latest = ExchangeRate::latest('id')->firstOrFail();

        expect(ExchangeRate::count())->toBe(2)
            ->and($latest->exchange_rate)->toBe(4100.0)
            ->and($latest->user_id)->toBe($this->admin->id)
            ->and(ExchangeRateResource::getPages())->not->toHaveKey('edit');

        Livewire::test(ListExchangeRates::class)
            ->assertCanSeeTableRecords(ExchangeRate::all())
            ->assertSee('KHR');
    });

    it('rejects a rate that is not above zero', function () {
        Livewire::test(CreateExchangeRate::class)
            ->fillForm(['branch_id' => $this->branch->id, 'exchange_rate' => 0, 'exchange_rate_currency' => 1])
            ->call('create')
            ->assertHasFormErrors(['exchange_rate']);
    });
});

describe('exchange money', function () {
    it('calculates the riels of a dollar exchange on the server', function () {
        $rate = ExchangeRate::create(['exchange_rate' => 4100, 'exchange_rate_currency' => 1, 'branch_id' => $this->branch->id]);

        Livewire::test(CreateExchangeMoney::class)
            ->fillForm([
                'branch_id' => $this->branch->id,
                'exchange_type' => ExchangeMoney::USD_TO_KHR,
                'amount_exchange' => 100,
                'rate_exchange' => 4100,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $exchange = ExchangeMoney::firstOrFail();

        expect($exchange->total_amount)->toBe(410000.0)
            ->and($exchange->exchange_id)->toBe($rate->id)
            ->and($exchange->exchange_date->isToday())->toBeTrue()
            ->and($exchange->user_id)->toBe($this->admin->id);
    });

    it('calculates the dollars of a riel exchange on the server', function () {
        Livewire::test(CreateExchangeMoney::class)
            ->fillForm([
                'branch_id' => $this->branch->id,
                'exchange_type' => ExchangeMoney::KHR_TO_USD,
                'amount_exchange' => 410000,
                'rate_exchange' => 4100,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(ExchangeMoney::firstOrFail()->total_amount)->toBe(100.0);
    });

    it('redirects to the receipt when creating and printing', function () {
        Livewire::test(CreateExchangeMoney::class)
            ->fillForm([
                'branch_id' => $this->branch->id,
                'exchange_type' => ExchangeMoney::USD_TO_KHR,
                'amount_exchange' => 10,
                'rate_exchange' => 4000,
            ])
            ->call('createAndPrint')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('exchange-money.receipt', ['id' => 1]));

        expect(ExchangeMoney::count())->toBe(1);
    });

    it('only edits an exchange made today and recalculates its total', function () {
        $today = ExchangeMoney::create([
            'amount_exchange' => 10, 'rate_exchange' => 4000, 'total_amount' => 40000, 'exchange_type' => 1,
            'exchange_date' => today(), 'branch_id' => $this->branch->id,
        ]);
        $past = ExchangeMoney::create([
            'amount_exchange' => 10, 'rate_exchange' => 4000, 'total_amount' => 40000, 'exchange_type' => 1,
            'exchange_date' => today()->subDay(), 'branch_id' => $this->branch->id,
        ]);

        Livewire::test(EditExchangeMoney::class, ['record' => $today->getKey()])
            ->fillForm(['amount_exchange' => 20])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($today->refresh()->total_amount)->toBe(80000.0)
            ->and($today->user_update)->toBe($this->admin->id);

        Livewire::test(EditExchangeMoney::class, ['record' => $past->getKey()])->assertForbidden();

        Livewire::test(ListExchangeMoney::class)
            ->assertActionVisible(TestAction::make('edit')->table($today))
            ->assertActionHidden(TestAction::make('edit')->table($past));
    });

    it('prints a receipt only for the branches of the user', function () {
        $exchange = ExchangeMoney::create([
            'amount_exchange' => 10, 'rate_exchange' => 4000, 'total_amount' => 40000, 'exchange_type' => 1,
            'exchange_date' => today(), 'branch_id' => $this->branch->id,
        ]);

        // Filament only lets users that are not `FilamentUser`s through its auth middleware locally.
        config(['app.env' => 'local']);

        $this->get(route('exchange-money.receipt', ['id' => $exchange->id]))
            ->assertOk()
            ->assertSee('40,000');

        $user = User::factory()->create(['is_admin' => 0]);
        $user->branch()->attach(Branch::create(['name_en' => 'Other', 'is_active' => true])->id);
        $this->actingAs($user)
            ->get(route('exchange-money.receipt', ['id' => $exchange->id]))
            ->assertNotFound();
    });
});

describe('over money', function () {
    it('creates an over money entry dated today', function () {
        Livewire::test(CreateOverMoney::class)
            ->fillForm(['branch_id' => $this->branch->id, 'over_amount' => 500, 'over_money_type' => OverMoney::KHR])
            ->call('create')
            ->assertHasNoFormErrors();

        $overMoney = OverMoney::firstOrFail();

        expect($overMoney->over_money_date->isToday())->toBeTrue()
            ->and($overMoney->user_id)->toBe($this->admin->id);
    });

    it('only edits an entry made today', function () {
        $today = OverMoney::create(['over_amount' => 5, 'over_money_type' => 1, 'over_money_date' => today(), 'branch_id' => $this->branch->id]);
        $past = OverMoney::create(['over_amount' => 5, 'over_money_type' => 1, 'over_money_date' => today()->subDay(), 'branch_id' => $this->branch->id]);

        Livewire::test(EditOverMoney::class, ['record' => $today->getKey()])
            ->fillForm(['over_amount' => 9])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($today->refresh()->over_amount)->toBe(9.0)
            ->and($today->user_update)->toBe($this->admin->id);

        Livewire::test(EditOverMoney::class, ['record' => $past->getKey()])->assertForbidden();

        Livewire::test(ListOverMoney::class)->assertCanSeeTableRecords([$today, $past]);
    });

    it('filters by date range', function () {
        $today = OverMoney::create(['over_amount' => 5, 'over_money_type' => 1, 'over_money_date' => today(), 'branch_id' => $this->branch->id]);
        $past = OverMoney::create(['over_amount' => 5, 'over_money_type' => 1, 'over_money_date' => today()->subDays(10), 'branch_id' => $this->branch->id]);

        Livewire::test(ListOverMoney::class)
            ->filterTable('filter_date', ['from_date' => today()->subDay()->toDateString(), 'to_date' => today()->toDateString()])
            ->assertCanSeeTableRecords([$today])
            ->assertCanNotSeeTableRecords([$past]);
    });
});

describe('change products', function () {
    beforeEach(function () {
        $this->location = Location::create(['name' => 'Shop', 'name_kh' => 'ហាង', 'branch_id' => $this->branch->id]);
        $this->metric = Metric::create(['name' => 'Box', 'name_kh' => 'ប្រអប់', 'qty' => 12, 'name_show' => 'Box', 'branch_id' => $this->branch->id]);
        DB::table('inventories')->insert(['name' => 'Item', 'name_kh' => 'ទំនិញ', 'branch_id' => $this->branch->id]);
        $this->productId = DB::table('inventories')->value('id');
    });

    it('creates a cash exchange without a product and calculates the total', function () {
        Livewire::test(CreateChangeProduct::class)
            ->fillForm([
                'branch_id' => $this->branch->id,
                'change_product_type' => ChangeProduct::TYPE_CASH,
                'change_description' => 'Cash reward',
                'change_product_date' => today()->toDateString(),
                'qty' => 3,
                'amount' => 2500,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $changeProduct = ChangeProduct::firstOrFail();

        expect($changeProduct->total_amount)->toBe(7500.0)
            ->and($changeProduct->submit_status)->toBe(0)
            ->and($changeProduct->user_id)->toBe($this->admin->id);
    });

    it('needs the stock, product and metric for a goods exchange', function () {
        Livewire::test(CreateChangeProduct::class)
            ->fillForm([
                'branch_id' => $this->branch->id,
                'change_product_type' => ChangeProduct::TYPE_ITEM,
                'change_product_date' => today()->toDateString(),
                'qty' => 1,
                'amount' => 1000,
            ])
            ->call('create')
            ->assertHasFormErrors(['location_id' => 'required', 'inventory_id' => 'required']);
    });

    it('locks a record once its stock is submitted', function () {
        $open = makeControlChangeProduct($this);
        $submitted = makeControlChangeProduct($this, ['submit_status' => 1]);

        Livewire::test(ListChangeProducts::class)
            ->assertActionVisible(TestAction::make('edit')->table($open))
            ->assertActionHidden(TestAction::make('edit')->table($submitted))
            ->assertActionHidden(TestAction::make('submitStock')->table($submitted));

        Livewire::test(EditChangeProduct::class, ['record' => $submitted->getKey()])->assertForbidden();
    });

    it('offers to submit the stock of goods exchanges only', function () {
        $item = makeControlChangeProduct($this);
        $cash = makeControlChangeProduct($this, ['change_product_type' => ChangeProduct::TYPE_CASH, 'inventory_id' => null, 'metric_id' => null, 'location_id' => null]);

        Livewire::test(ListChangeProducts::class)
            ->assertActionVisible(TestAction::make('submitStock')->table($item))
            ->assertActionHidden(TestAction::make('submitStock')->table($cash));
    });

    it('keeps the record open when the stock cannot be taken', function () {
        $item = makeControlChangeProduct($this);

        Livewire::test(ListChangeProducts::class)
            ->callAction(TestAction::make('submitStock')->table($item))
            ->assertNotified();

        expect($item->refresh()->submit_status)->toBe(0);
    });

    it('takes the stock and locks the record on submit', function () {
        $item = makeControlChangeProduct($this);
        DB::table('inventory_stocks')->insert([
            'inventory_id' => $this->productId,
            'location_id' => $this->location->id,
            'metric_id' => $this->metric->id,
            'quantity' => 100,
        ]);

        Livewire::test(ListChangeProducts::class)
            ->callAction(TestAction::make('submitStock')->table($item))
            ->assertNotified();

        expect($item->refresh()->submit_status)->toBe(1)
            ->and($item->user_update)->toBe($this->admin->id)
            ->and((float) DB::table('inventory_stocks')->value('quantity'))->toBe(76.0);
    });

    it('prints the selected records on one receipt', function () {
        $first = makeControlChangeProduct($this);
        $second = makeControlChangeProduct($this, ['change_product_type' => ChangeProduct::TYPE_CASH, 'inventory_id' => null, 'metric_id' => null, 'change_description' => 'Cash reward']);

        Livewire::test(ListChangeProducts::class)
            ->selectTableRecords([$first->id, $second->id])
            ->callAction(TestAction::make('printSelected')->table()->bulk())
            ->assertRedirect(route('change-product.receipt', ['ids' => [$first->id, $second->id]]));

        config(['app.env' => 'local']);

        $this->get(route('change-product.receipt', ['ids' => [$first->id, $second->id]]))
            ->assertOk()
            ->assertSee('ទំនិញ')
            ->assertSee('Cash reward')
            ->assertSee('4,000');

        $this->get(route('change-product.receipt', ['ids' => [999]]))->assertNotFound();
    });
});
