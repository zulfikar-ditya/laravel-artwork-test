<?php

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

it('can get datatable', function () {
    $repository = new UserRepository();
    $request = new Request([
        'search' => 'search term',
        'order' => '-name',
        'per_page' => 10,
    ]);

    $result = $repository->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a User', function () {
    $repository = new UserRepository();
    $data = User::factory()->make()->toArray();

    $result = $repository->create([
        ...$data,
        'password' => 'password',
    ]);

    expect($result)->toBeInstanceOf(User::class);
    expect($result->name)->toEqual($data['name']);

    $this->assertDatabaseHas('users', [
        'name' => $data['name'],
    ]);
});

it('can update a User', function () {
    $repository = new UserRepository();
    $User = User::factory()->create();

    $data = [
        'name' => 'Jane Smith',
    ];

    $result = $repository->update($data, $User->id);

    expect($result)->toBeInstanceOf(User::class);
    expect($result->name)->toEqual('Jane Smith');

    $this->assertDatabaseHas('users', [
        'name' => 'Jane Smith',
    ]);
});

it('can delete a User', function () {
    $repository = new UserRepository();
    $User = User::factory()->create();

    $repository->delete($User->id);

    expect(User::find($User->id))->toBeNull();

    $this->assertDatabaseMissing('users', [
        'name' => $User->name,
    ]);
});
