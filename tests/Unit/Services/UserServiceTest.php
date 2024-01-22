<?php

use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Interfaces\Services\UserServiceInterface;
use App\Models\User;
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

    $service = app(UserServiceInterface::class);

    $result = $service->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a User', function () {

    $fake = User::factory()->make()->toArray();

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        [
            ...$fake,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        (new StoreUserRequest())->rules()
    ));

    $service = app(UserServiceInterface::class);

    $result = $service->create($request);

    $this->assertDatabaseHas('users', [
        'name' => $fake['name'],
    ]);
});

it('can update a User', function () {
    $User = User::factory()->create();

    $fake = User::factory()->make()->toArray();

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        $fake,
        (new UpdateUserRequest())->rules()
    ));

    $service = app(UserServiceInterface::class);

    $result = $service->update($request, $User);

    $this->assertDatabaseHas('users', [
        'name' => $fake['name'],
    ]);
});

it('can delete a User', function () {
    $User = User::factory()->create();

    $service = app(UserServiceInterface::class);

    $result = $service->delete($User);

    expect(User::find($User->id))->toBeNull();

    $this->assertDatabaseMissing('users', [
        'name' => $User->name,
    ]);
});
