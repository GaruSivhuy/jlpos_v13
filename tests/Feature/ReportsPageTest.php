<?php

use App\Filament\Pages\Reports;
use App\Models\Branch;
use App\Models\OverMoney;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema as DbSchema;
use Livewire\Livewire;

function createReportsLegacyTable(string $name, Closure $columns): void
{
    if (! DbSchema::hasTable($name)) {
        DbSchema::create($name, $columns);
    }
}

beforeEach(function () {
    if (! DbSchema::hasColumn('users', 'is_admin')) {
        DbSchema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->boolean('is_admin')->default(false);
        });
    }

    createReportsLegacyTable('branch', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name_en');
        $table->string('name_kh')->nullable();
        $table->boolean('is_active')->nullable();
        $table->timestamps();
    });

    createReportsLegacyTable('branchables', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('branch_id');
        $table->string('branchables_type');
        $table->unsignedBigInteger('branchables_id');
        $table->timestamps();
    });

    createReportsLegacyTable('main_categories', function (Blueprint $table) {
        $table->increments('id');
        $table->string('cat_name');
        $table->string('cat_name_kh')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('user_updated')->nullable();
        $table->integer('branch_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    createReportsLegacyTable('over_money', function (Blueprint $table) {
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

    Filament::setCurrentPanel('admin');

    $this->admin = User::factory()->create(['is_admin' => 1]);
    $this->branch = Branch::create(['name_en' => 'Main', 'name_kh' => 'ចម្បង', 'is_active' => true]);
    $this->actingAs($this->admin);
});

it('defines a required, live report type select and date range pickers', function () {
    $page = new Reports;

    $components = $page->form(Schema::make($page))->getFlatComponents();

    expect($components['report_type']->isRequired())->toBeTrue()
        ->and($components['report_type']->isLive())->toBeTrue()
        ->and($components['from_date'])->not->toBeNull()
        ->and($components['to_date'])->not->toBeNull();
});

it('always shows the branch, main category and user filters alongside the report type, with inline labels in a single column', function () {
    $page = new Reports;

    $schema = $page->form(Schema::make($page));
    $components = $schema->getFlatComponents();

    expect($components['branch_id'])->not->toBeNull()
        ->and($components['branch_id']->isVisible())->toBeTrue()
        ->and($components['main_cat_id'])->not->toBeNull()
        ->and($components['main_cat_id']->isVisible())->toBeTrue()
        ->and($components['user_id'])->not->toBeNull()
        ->and($components['user_id']->isVisible())->toBeTrue();

    $grid = $schema->getComponents()[0]->getChildSchema()->getComponents()[0];

    expect($grid->getColumns('lg'))->toBe(1)
        ->and($grid->hasInlineLabel())->toBeTrue();
});

it('renders the reports page', function () {
    Livewire::test(Reports::class)->assertSuccessful();
});

it('opens the print page in a new tab for a screen report', function () {
    $fromDate = today()->toDateString();
    $toDate = today()->toDateString();

    $expectedUrl = route('reports.print', [
        'report_type' => 'rpt_over_money',
        'from_date' => $fromDate,
        'to_date' => $toDate,
    ]);

    Livewire::test(Reports::class)
        ->fillForm([
            'report_type' => 'rpt_over_money',
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ])
        ->call('openPrint')
        ->assertHasNoFormErrors()
        ->assertJs('window.open('.json_encode($expectedUrl).", '_blank')");
});

it('requires a report type before opening the print page', function () {
    Livewire::test(Reports::class)
        ->call('openPrint')
        ->assertHasFormErrors(['report_type' => 'required']);
});

it('requires the main category only for the main category report', function () {
    Livewire::test(Reports::class)
        ->fillForm([
            'report_type' => 'rpt_sale_by_main_cat_detail',
            'from_date' => today()->toDateString(),
            'to_date' => today()->toDateString(),
        ])
        ->call('openPrint')
        ->assertHasFormErrors(['main_cat_id' => 'required']);
});

it('filters print results by the selected branch', function () {
    // Filament only lets users that are not `FilamentUser`s through its auth middleware locally.
    config(['app.env' => 'local']);

    $otherBranch = Branch::create(['name_en' => 'Other', 'name_kh' => 'ផ្សេង', 'is_active' => true]);

    $own = OverMoney::create([
        'over_amount' => 100, 'over_money_type' => OverMoney::USD,
        'over_money_date' => today(), 'branch_id' => $this->branch->id,
    ]);
    $foreign = OverMoney::create([
        'over_amount' => 200, 'over_money_type' => OverMoney::USD,
        'over_money_date' => today(), 'branch_id' => $otherBranch->id,
    ]);

    $this->get(route('reports.print', [
        'report_type' => 'rpt_over_money',
        'from_date' => today()->toDateString(),
        'to_date' => today()->toDateString(),
        'branch_id' => $this->branch->id,
    ]))
        ->assertOk()
        ->assertSee(number_format($own->over_amount, 2))
        ->assertDontSee(number_format($foreign->over_amount, 2));
});

it('prints an over money report grouped by date and type', function () {
    config(['app.env' => 'local']);

    OverMoney::create([
        'over_amount' => 100, 'over_money_type' => OverMoney::USD,
        'over_money_date' => today(), 'branch_id' => $this->branch->id,
    ]);

    $this->get(route('reports.print', [
        'report_type' => 'rpt_over_money',
        'from_date' => today()->toDateString(),
        'to_date' => today()->toDateString(),
    ]))
        ->assertOk()
        ->assertSee(today()->format('d-m-Y'))
        ->assertSee('100.00');
});

it('rejects an unknown report type on the print route', function () {
    config(['app.env' => 'local']);

    $this->get(route('reports.print', [
        'report_type' => 'rpt_stock',
        'from_date' => today()->toDateString(),
        'to_date' => today()->toDateString(),
    ]))->assertSessionHasErrors('report_type');
});
