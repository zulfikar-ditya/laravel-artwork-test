<?php

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

it('can get datatable', function () {
    $repository = new RoleRepository();
    $request = new Request([
        'search' => 'search term',
        'order' => '-name',
        'per_page' => 10,
    ]);

    $result = $repository->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a Role', function () {
    $repository = new RoleRepository();
    $data = Role::factory()->make()->toArray();

    $result = $repository->create($data);

    expect($result)->toBeInstanceOf(Role::class);
    expect($result->name)->toEqual($data['name']);

    $this->assertDatabaseHas('roles', [
        'name' => $data['name'],
    ]);
});

it('can update a Role', function () {
    $repository = new RoleRepository();
    $Role = Role::factory()->create();

    $data = [
        'name' => 'Jane Smith',
    ];

    $result = $repository->update($data, $Role->id);

    expect($result)->toBeInstanceOf(Role::class);
    expect($result->name)->toEqual('Jane Smith');

    $this->assertDatabaseHas('roles', [
        'name' => 'Jane Smith',
    ]);
});

it('can delete a Role', function () {
    $repository = new RoleRepository();
    $Role = Role::factory()->create();

    $repository->delete($Role->id);

    expect(Role::find($Role->id))->toBeNull();

    $this->assertDatabaseMissing('roles', [
        'name' => $Role->name,
    ]);
});
