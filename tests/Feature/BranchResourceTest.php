<?php

use App\Filament\Resources\Branch\Pages\CreateBranch;
use App\Filament\Resources\Branch\Pages\EditBranch;
use App\Filament\Resources\Branch\Pages\ListBranches;
use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    // The legacy branch table exists in the real database but not in the repo migrations.
    if (! Schema::hasTable('branch')) {
        Schema::create('branch', function ($table) {
            $table->increments('id');
            $table->string('name_en');
            $table->string('name_kh')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
        });
    }

    Filament::setCurrentPanel('admin');
    $this->actingAs(User::factory()->create());
});

it('lists branches', function () {
    $branch = Branch::create(['name_en' => 'Main', 'name_kh' => 'ចម្បង', 'is_active' => true]);

    Livewire::test(ListBranches::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$branch])
        ->assertSee('Main')
        ->assertSee('ចម្បង');
});

it('creates a branch', function () {
    Livewire::test(CreateBranch::class)
        ->fillForm(['name_en' => 'Second', 'name_kh' => 'ទីពីរ', 'is_active' => true])
        ->call('create')
        ->assertHasNoFormErrors();

    $branch = Branch::where('name_en', 'Second')->firstOrFail();

    expect($branch->name_kh)->toBe('ទីពីរ')
        ->and($branch->is_active)->toBeTrue();
});

it('creates an inactive branch when the toggle is off', function () {
    Livewire::test(CreateBranch::class)
        ->fillForm(['name_en' => 'Dormant', 'is_active' => false])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Branch::where('name_en', 'Dormant')->firstOrFail()->is_active)->toBeFalse();
});

it('requires a unique latin name', function () {
    Branch::create(['name_en' => 'Main', 'is_active' => true]);

    Livewire::test(CreateBranch::class)
        ->fillForm(['name_en' => ''])
        ->call('create')
        ->assertHasFormErrors(['name_en' => 'required']);

    Livewire::test(CreateBranch::class)
        ->fillForm(['name_en' => 'Main'])
        ->call('create')
        ->assertHasFormErrors(['name_en' => 'unique']);
});

it('updates a branch and allows keeping its own name', function () {
    $branch = Branch::create(['name_en' => 'Main', 'name_kh' => 'ចម្បង', 'is_active' => true]);

    Livewire::test(EditBranch::class, ['record' => $branch->getKey()])
        ->fillForm(['name_kh' => 'ថ្មី', 'is_active' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $branch->refresh();

    expect($branch->name_en)->toBe('Main')
        ->and($branch->name_kh)->toBe('ថ្មី')
        ->and($branch->is_active)->toBeFalse();
});
