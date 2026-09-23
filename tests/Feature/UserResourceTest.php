<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // These legacy tables/columns exist in the real database but not in the repo migrations.
    if (! Schema::hasColumn('users', 'active')) {
        Schema::table('users', function ($table) {
            $table->boolean('active')->default(true);
            $table->boolean('is_admin')->default(false);
        });
    }

    if (! Schema::hasTable('branch')) {
        Schema::create('branch', function ($table) {
            $table->increments('id');
            $table->string('name_en');
            $table->string('name_kh')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
        });
        Schema::create('branchables', function ($table) {
            $table->increments('id');
            $table->string('branch_id');
            $table->morphs('branchables');
            $table->timestamps();
        });
    }

    Filament::setCurrentPanel('admin');
    $this->actingAs(User::factory()->create());

    $this->role = Role::create(['name' => 'cashier', 'guard_name' => 'web']);
    $this->branch = Branch::create(['name_en' => 'Main', 'name_kh' => 'ចម្បង', 'is_active' => 1]);
});

it('lists users with their roles and branches', function () {
    $user = User::factory()->create();
    $user->assignRole($this->role);
    $user->branch()->sync([$this->branch->id]);

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$user])
        ->assertSee('cashier')
        ->assertSee('Main');
});

it('creates a user with hashed password, roles and branches', function () {
    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'New Cashier',
            'email' => 'cashier@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
            'roles' => [$this->role->id],
            'branch' => [$this->branch->id],
            'active' => true,
            'is_admin' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'cashier@example.com')->firstOrFail();

    expect(Hash::check('secret-pass', $user->password))->toBeTrue()
        ->and($user->hasRole('cashier'))->toBeTrue()
        ->and($user->branch->pluck('id')->all())->toBe([$this->branch->id])
        ->and($user->active)->toBeTrue()
        ->and($user->is_admin)->toBeFalse();
});

it('validates required fields, unique email and password confirmation on create', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => '',
            'email' => 'taken@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'different',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'unique',
            'password' => 'confirmed',
            'roles' => 'required',
            'branch' => 'required',
        ]);
});

it('updates a user without touching the password', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);
    $user->assignRole($this->role);
    $user->branch()->sync([$this->branch->id]);
    $originalPassword = $user->password;

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->assertFormFieldIsHidden('password')
        ->fillForm(['email' => 'new@example.com', 'active' => false, 'is_admin' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->email)->toBe('new@example.com')
        ->and($user->active)->toBeFalse()
        ->and($user->is_admin)->toBeTrue()
        ->and($user->password)->toBe($originalPassword)
        ->and($user->hasRole('cashier'))->toBeTrue();
});
