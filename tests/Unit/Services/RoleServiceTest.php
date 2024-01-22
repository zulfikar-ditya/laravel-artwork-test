<?php

use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Interfaces\Services\RoleServiceInterface;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

it('can get datatable', function () {
    $request = new Request([
        'search' => 'search term',
        'order' => '-name',
        'per_page' => 10,
    ]);

    $service = app(RoleServiceInterface::class);

    $result = $service->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a Role', function () {

    $fake = Role::factory()->make()->toArray();

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        $fake,
        (new StoreRoleRequest())->rules()
    ));

    $service = app(RoleServiceInterface::class);

    $result = $service->create($request);

    $this->assertDatabaseHas('roles', [
        'name' => $fake['name'],
    ]);
});

it('can update a Role', function () {
    $Role = Role::factory()->create();

    $fake = Role::factory()->make()->toArray();

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        $fake,
        (new StoreRoleRequest())->rules()
    ));

    $service = app(RoleServiceInterface::class);

    $result = $service->update($request, $Role);

    $this->assertDatabaseHas('roles', [
        'name' => $fake['name'],
    ]);
});

it('can delete a Role', function () {
    $Role = Role::factory()->create();

    $service = app(RoleServiceInterface::class);

    $result = $service->delete($Role);

    expect(Role::find($Role->id))->toBeNull();

    $this->assertDatabaseMissing('roles', [
        'name' => $Role->name,
    ]);
});
