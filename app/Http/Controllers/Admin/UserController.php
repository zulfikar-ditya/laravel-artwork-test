<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Instantiate a new Controllers instance.
     */
    public function __construct()
    {
        // $this->policyModel = App\Models\User::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserServiceInterface $userService)
    {
        // $this->authorize('viewAny', App\Models\User::class);

        return $this->responseJson($userService->datatable(request()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // $this->authorize('create', App\Models\User::class);

        DB::beginTransaction();

        try {
            $userService = app(UserServiceInterface::class);
            $userService->create($request);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'create', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'create');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // $this->authorize('view', $user);

        $user->load('roles');

        return $this->responseJsonData($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // $this->authorize('update', $user);

        DB::beginTransaction();

        try {
            $userService = app(UserServiceInterface::class);
            $userService->update($request, $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'update', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // $this->authorize('delete', $user);

        DB::beginTransaction();

        try {
            $userService = app(UserServiceInterface::class);
            $userService->delete($user);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'delete', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'delete');
    }
}
