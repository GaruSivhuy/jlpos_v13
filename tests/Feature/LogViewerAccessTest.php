<?php

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\Models\Role;

it('forbids guests from opening the log viewer', function () {
    $this->get(route('log-viewer.index'))->assertForbidden();
});

it('forbids users who are not super admins from opening the log viewer', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'cashier', 'guard_name' => 'web']));

    $this->actingAs($user)->get(route('log-viewer.index'))->assertForbidden();
});

it('lets super admins open the log viewer', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => Utils::getSuperAdminName(), 'guard_name' => 'web']));

    $this->actingAs($user)->get(route('log-viewer.index'))->assertOk();
});
