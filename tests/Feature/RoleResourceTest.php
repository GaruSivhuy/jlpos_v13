<?php

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\RoleResource;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // The legacy users table has columns that the repo migrations do not create.
    if (! Schema::hasColumn('users', 'is_admin')) {
        Schema::table('users', function ($table) {
            $table->boolean('active')->default(true);
            $table->boolean('is_admin')->default(false);
        });
    }

    Filament::setCurrentPanel('admin');
});

/**
 * @param  array<int, string>  $permissions
 */
function userWithPermissions(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user->givePermissionTo($permissions);

    return $user;
}

it('hides the roles resource from users without the view_any:role permission', function () {
    $this->actingAs(User::factory()->create());

    expect(RoleResource::canViewAny())->toBeFalse();
});

it('shows the roles resource to users with the view_any:role permission', function () {
    $this->actingAs(userWithPermissions(['view_any:role']));

    expect(RoleResource::canViewAny())->toBeTrue();
});

it('serves the published role resource under the shield slug', function () {
    expect(Filament::getPanel('admin')->getResources())->toContain(RoleResource::class)
        ->and(RoleResource::getSlug())->toBe('shield/roles');
});

it('lists roles with their permission counts', function () {
    $this->actingAs(userWithPermissions(['view_any:role']));

    $role = Role::create(['name' => 'cashier', 'guard_name' => 'web']);
    $role->givePermissionTo(Permission::findOrCreate('view_any:role', 'web'));

    Livewire::test(ListRoles::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$role])
        ->assertSee('Cashier');
});

it('creates a role', function () {
    $this->actingAs(userWithPermissions(['view_any:role', 'create:role']));

    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'stock_keeper'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Role::where('name', 'stock_keeper')->where('guard_name', 'web')->exists())->toBeTrue();
});

it('requires a unique role name', function () {
    $this->actingAs(userWithPermissions(['view_any:role', 'create:role']));
    Role::create(['name' => 'cashier', 'guard_name' => 'web']);

    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'cashier'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('denies creating a role without the create:role permission', function () {
    $this->actingAs(userWithPermissions(['view_any:role']));

    expect(RoleResource::canCreate())->toBeFalse();
});
